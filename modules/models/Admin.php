<?php 
	
	namespace app\modules\models;

	use yii\db\ActiveRecord;

	use Yii;
	
	class Admin extends ActiveRecord{

		public $rememberMe = true;

		// 添加属性
		public $repass;
		public static function tableName(){

			return '{{%admin}}';
		} 

		public function attributeLabels(){
		    return [
		        'adminuser' => '管理员账号',
		        'adminemail' => '管理员邮箱',
		        'adminpass' => '管理员密码',
		        'repass' => '确认密码',
		    ];
		}

		// 验证规则
		public function rules(){
			return [
				['adminuser','required','message' => '管理员账号不能为空','on' => ['login','seekpass','changepass','adminadd','changeemail']],
				['adminuser','unique','message' => '管理员已被注册','on' => 'adminadd'],
				['adminpass','required','message' => '管理员密码不能为空','on'=> ['login','changepass','adminadd','changeemail']],
				['rememberMe','boolean','on'=>'login'],
				['adminpass','validatePass','on'=>['login','changeemail']],
				['adminemail','required','message' => '电子邮箱不能为空','on'=>['seekpass','adminadd','changeemail']],
				['adminemail','email','message' => '电子邮箱格式不正确','on'=>['seekpass','adminadd','changeemail']],
				['adminemail','unique','message' => '电子邮箱已被注册','on'=>['adminadd','changeemail']],
				['adminemail','validateEmail','on'=>'seekpass'],		
				['repass','required','message' => '确认密码不能不为空','on' => ['changepass','adminadd']],
				['repass','compare','compareAttribute' => 'adminpass','message'=>'两次输入密码不一致','on' => ['changepass','adminadd']],
			];
		}

		public function validatePass(){
			if(!$this -> hasErrors()){//如果前三项没有错误
				$data = self::find()
					//绑定上数据
					->where('adminuser = :user and adminpass = :pass',[":user" => $this->adminuser,':pass' => md5($this->adminpass)])
					->one();

				if(is_null($data)){//如果有查询出来就是一个对象，否则就为空
					$this->addError("adminpass","用户名或者密码错误");
				}
			}
		}

		public function validateEmail(){
			if(!$this -> hasErrors()){//如果前三项没有错误
				$data = self::find()
					//绑定上数据
					->where('adminuser = :user and adminemail = :email',[":user" => $this->adminuser,':email' => $this->adminemail])
					->one();

				if(is_null($data)){//如果有查询出来就是一个对象，否则就为空
					$this->addError("adminemail","管理员电子邮箱不匹配");
				}
			}
		}

		// 登录表单验证
		public function login($data){

			// 指定属于自己的验证场景:登录时不需要用到找回密码的ruels验证字段
			$this->scenario = "login";

			//载入数据 && 验证数据
			if($this->load($data) && $this->validate()){

				//登录有效期
				$lifetime = $this->rememberMe ? 24*3600 : 0;

				//操作session
				$session = Yii::$app->session;
				session_set_cookie_params($lifetime);

				//存入session
				$session['admin'] = [
					'adminuser' => $this->adminuser,
					'isLogin' => 1,
				];

				//更新登录时间、登录ip
				$this->updateAll(['logintime'=>time(),'loginip'=>ip2long(Yii::$app->request->userIP)],'adminuser = :user',[':user' =>$this->adminuser]);

				return (bool)$session['admin']['isLogin'];

			}

			return false;
		}

		//找回密码
		public function seekPass($data){

			$this->scenario = "seekpass";

			if($this->load($data) && $this->validate()){

				//给管理员的邮箱发送邮件
				$time = time();
				// 传递token值,自己定义的生成规则
				$token = $this->createToken($data['Admin']['adminuser'],$time);

				//compose第一个参数：模板 第二个参数：传递的变量数组
				$mailer = Yii::$app->mailer->compose('seekpass',['adminuser' => $data['Admin']['adminuser'],'time' => $time,'token' => $token]);
				$mailer->setFrom('zengzhuoyu24@163.com');//发送邮件的邮箱
				$mailer->setTo($data['Admin']['adminemail']);//发送给谁
				$mailer->setSubject('慕课商城-找回密码');
				if($mailer->send()){
					return true;
				}
			}

			return false;
		}

		// 生成token
		public function createToken($adminuser,$time){

        			return md5(md5($adminuser).base64_encode(Yii::$app->request->userIP).md5($time));
		}

		// 修改密码
		public function changePass($data){

			$this->scenario = "changepass";
			if($this->load($data) && $this->validate()){

				//修改密码
            			return (bool)$this->updateAll(['adminpass' => md5($this->adminpass)], 'adminuser = :user', [':user' => $this->adminuser]);
			}

			return false;
		}

		//添加管理员
		public function reg($data){

			$this->scenario = 'adminadd';

			if($this->load($data) && $this->validate()){
				$this->adminpass = md5($this->adminpass);
				if($this->save(false)){
					return true;
				}
				return false;
			}
			return false;
		}

		//当前登录管理员邮箱的修改
		public function changeEmail($data)
		{
		    $this->scenario = "changeemail";
		    if ($this->load($data) && $this->validate()) {
		        return (bool)$this->updateAll(['adminemail' => $this->adminemail], 'adminuser = :user', [':user' => $this->adminuser]);
		    }
		    return false;
		}

	}
 ?>