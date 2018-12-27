<?php

class Employee_Model extends CI_Model{

    function __construct(){
        
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->library('email');
        $this->load->library('form_validation');
    }
    
    //insert employee details to employee table
    public function insertEmployee($data){
        
        return $this->db->insert('employee',$data);
      
    }
    
    public function loginUser($username, $password){
        //$this->db->where(array('username' = >$username, 'password' => $password));
        $query = $this->db->get_where('employee', array('username' => $username, 'password' => $password,'status'=> 0));   //status sholud be 1
        
        if($query->num_rows() == 1){
            
            $userArr = array();
            foreach($query->result() as $row){
                $userArr[0] = $row->emp_id;
                $userArr[1] = $row->emp_name;
                
            }
            $userData = array(
                'emp_id' => $userArr[0],
                'emp_name' => $userArr[1],
                'logged_in'=> TRUE
            );
            $this->session->set_userdata($userData);
            
            return $query->result();
        }else{
            return false;
        }
    }
    
    
    //send confirm mail
    public function sendEmail($receiver){
        $from = 'ngocmy.truong29@gmail.com';    //senders email address
        $subject = 'Verify email address';  //email subject
        
        //sending confirmEmail($receiver) function calling link to the user, inside message body
        $message = 'Dear User,<br><br> Please click on the below activation link to verify your email address<br><br>
        <a href=\'http://www.localhost/Email/Signup_Controller/confirmEmail/'.md5($receiver).'\'>http://www.localhost/Email/Signup_Controller/confirmEmail/'. md5($receiver) .'</a><br><br>Thanks';
        
        
        
        //config email settings
        $config['protocol'] = 'smtp';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['smtp_host'] = 'ssl://smtp.gmail.com';
        $config['smtp_port'] = '465';
        $config['smtp_user'] = $from;
        $config['_smtp_auth'] = TRUE;
        $config['smtp_pass'] = 'tnm29199#'; 
        $config['mailtype'] = 'html';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = 'TRUE';
        
        $this->load->library('email', $config);

		$this->email->initialize($config);
        $this->email->set_newline("\r\n");
        //send email
        $this->email->from($from);
        $this->email->to($receiver);
        $this->email->subject($subject);
        $this->email->message($message);
        
        if($this->email->send()){
			//for testing
            echo "sent to: ".$receiver."<br>";
			echo "from: ".$from. "<br>";
			echo "protocol: ". $config['protocol']."<br>";
			echo "message: ".$message;
            return true;
        }else{

            show_error($this->email->print_debugger());

            return false;
        }
        
       
    }
    
    //activate account
    function verifyEmail($key){
        $data = array('status' => 1);
        $this->db->where('md5(email)',$key);
        return $this->db->update('employee', $data);    //update status as 1 to make active user
    }
    
}

?>