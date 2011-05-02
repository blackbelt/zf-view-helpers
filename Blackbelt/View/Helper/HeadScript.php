<?php
class Blackbelt_View_Helper_HeadScript extends Zend_View_Helper_HeadScript
{
	protected $_baseUrl;
	protected $_env;
	protected $_version;

	public function setBaseUrl($url)
	{
		$this->_baseUrl = rtrim($url, "/") . "/";
		return $this;
	}

	public function getBaseUrl()
	{
		return $this->_baseUrl;
	}

	public function setEnvironment($env)
	{
		$this->_env = $env;
		return $this;
	}

	public function getEnvironment()
	{
		return $this->_env;
	}

	public function setVersion($version)
	{
		$this->_version = $version;
		return $this;
	}

	public function getVersion()
	{
		return $this->_version;
	}

    public function headScript($mode = Zend_View_Helper_HeadScript::FILE, $spec = null, $placement = 'APPEND', array $attrs = array(), $type = 'text/javascript')
    {
        return parent::headScript($mode, $spec, $placement, $attrs, $type);
    }

	protected function allowInclude($val)
	{
		$attrs = (array) $val->attributes;
		$env = (array) !empty($attrs['env']) ? $attrs['env'] : array();
		$currEnv = $this->getEnvironment();

		unset($val->attributes['env']);

		// append the file if it's allowed for the given environment
		// if there are no environments defined, assume its all environments
		if (empty($env) || empty($currEnv) ||in_array($currEnv, $env)) {
			return true;
		}

		return false;
	}

	protected function getSourcePath($src)
	{
		$version = $this->getVersion();
		$baseurl = $this->getBaseUrl();

		$pathinfo = pathinfo($src);

		$version = (!empty($version)) ? $version . "." : "";

		$path = $this->getBaseUrl() . "{$pathinfo['dirname']}/{$pathinfo['filename']}.$version{$pathinfo['extension']}";

		// strip duplicate "/" easier to do this because the source path may be relative
		$path = preg_replace('/\/{2,}$/', '/', $path);

		return $path;
	}

	public function prepend($val)
	{
		if ($this->allowInclude($val)) {
			$val->attributes['src'] = $this->getSourcePath($val->attributes['src']);
			parent::prepend($val);
		}
	}

	public function append($val)
	{
		if ($this->allowInclude($val)) {
			$val->attributes['src'] = $this->getSourcePath($val->attributes['src']);
			parent::append($val);
		}
	}
}