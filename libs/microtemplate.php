<?php
/*

	MicroTemplate (C). D'Hulster 2015-16 

		pre-process commands.
		
			#basepath (basepath);
			#include (filex[,filey]);
			#ifdef (variables);
			#endif
			
		post-process commands
		
			{vars} = replaced by assigned variables (via assign method)

*/

function str_string_between($string, $start, $end)
{
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);    
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

class Microplates
{
	private 	$m_template;
	private	$m_attr;
	private 	$m_commands;
	private 	$m_template_path;
	
	private 	$m_line;
	
	
	public function __construct()
	{
		$this->m_line=0;
		$this->m_template = '';
		$this->m_template_path = '';
		$this->m_attr 		= array();
		$this->m_commands = array( 
					'#include'=>0,
					'#basepath'=>1);
	}
		
	private function dump()
	{
	}
	
	private function dump_context($context)
	{
		if ($context!='')
			echo "<br><context : </br>$context</br>";
	}	
	
	private function error($error=0,$context='')
	{
		echo "<H1>ERROR handler</H1>an error occured<br>";
		$txt = '';
		switch ($error)
		{
			case 0:
				$txt = "include file not found : ".$context;
				$context = '';
				break;
			case 10 :
				$txt = "command not ended";
				break; 
			case 1 :
				$txt = "Empty file";
				break;
		}
		if ($this->m_line>0)
			echo "error line ".$this->m_line."<br>";
				
		echo "<b>$txt</b><br>";
		$this->dump_context($context);
					
		exit();
	}
	
	private function command_process(&$buffer,$fnct,$startpos)
	{
		// found command locate end.
		$i=$startpos; 
		$cmd = '';
		while (($i<strlen($buffer)) && ($buffer[$i]!=';')) $i++;
						
		if ($buffer[$i]!=';')
		{
			$this->error(10,$buffer);
		}
		else 
		{
			$cmd = substr($buffer, $startpos, $i-$startpos);
			$buffer = str_replace($cmd.';', '', $buffer);
					
			$p = str_string_between($cmd,'(',')');
			
			if (strpos($buffer,',')!==FALSE)
				$param = explode(',',$p);
			else
				$param = $p;
										
			switch ($fnct)
			{
				case 0 :
					$this->load_include($param);
					break;
				case 1 : 
					$this->set_path($param);
					break;
			}
		}
	}
	
	private function read($filename)
	{
		$this->m_line=0;
		$file =fopen($filename,"r");
		if ($file)
		{
			while (($buffer = fgets($file, 4096)) !== false)
			{ 
				$this->m_line++;
				$cmd_found = FALSE;
				
				foreach($this->m_commands as $command=>$fnct)
				{
					$pos = strpos($buffer,$command);
					if ($pos !== FALSE)
					{
						$cmd_found = TRUE;
						$this->command_process($buffer,$fnct,$pos);
					}
				} 
				if ($cmd_found) $buffer = trim($buffer," \n");
				if (strlen($buffer)>0)
					$this->m_template.= $buffer."\n";
			}
			fclose($file);
			if ($this->m_line==0)
			{
				$this->error(1,$filename);
			}
		}
		else 
		{
			$this->error(0,$filename);
		}		
	}
	
	/* Load file (clear all previous loaded template) */
	public function load($filename)
	{
		$this->m_template = '';
		$this->read($this->m_template_path.$filename);
	}
	
	/* Load file (append to any previous loaded template) */
	public function load_include($filename)
	{
		if (is_string($filename))
		{
			$this->read($this->m_template_path.$filename);
		}
		else
		{
			if (is_array($filename))
			{
				foreach($filename as $fn)
					$this->read($this->m_template_path.$fn);
			}
		}	
	}	
	
	public function set_path($path) 
	{
		if (is_string($path))
		{
			$this->m_template_path = $path;
		}
		else 
			if (is_array($path))
			{
				$this->m_template_path = $path[0];
			}
	}
	
	public function get_path() 
	{
		return $this->m_template_path;
	} 
	
	/* set variables values */
	public function assign($key,$value)
	{
		/*
		if ((is_array($key)) && (is_array($value)))
		{
			foreach($key as $k)
		}	
		*/
		$key = '{'.$key.'}';
		$this->m_attr[$key] = $value;
	}
	
	/* render templates */
	public function render($direct = true)
	{
		foreach($this->m_attr as $key=>$value)
		{
			$this->m_template = str_replace($key, $value, $this->m_template);
		}
		if ($direct)
			echo $this->m_template;
		else 
			return $this->m_template;
	}
}


?>
