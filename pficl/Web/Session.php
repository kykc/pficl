<?php

namespace pficl\Web
{
	/**
	 * Non-blocking session (unlike php blocking file session) (BEWARE)
	 */
	class Session
	{
		protected static $inst = NULL;

		protected $data;
		protected $sessionId;
		protected $sessionLifetimeInSeconds;
		protected $cookieName;
		protected $sessionStorage;

		protected function __construct(\pficl\Cache\IStorage $sessionStorage, $timeout, $cookieName)
		{
			$this->cookieName = $cookieName;
			$this->sessionLifetimeInSeconds = $timeout;
			$this->sessionStorage = $sessionStorage;

			if (!array_key_exists($this->getCookieName(), $_COOKIE))
			{
				$key = uniqid().'zz';
				$res = setcookie($this->getCookieName(), $key, strtotime('now') + $this->getSessionLifetimeInSeconds());
				$_COOKIE[$this->getCookieName()] = $key;
			}

			$key = $_COOKIE[$this->getCookieName()];
			$session = $this->sessionStorage->load($key);

			$this->sessionId = $key;
			$this->data = $session->getData();
		}

		/** @return \pficl\Collection\SafeAccess */
		public function data()
		{
			return \pficl\Collection\SafeAccess::make($this->data);
		}

		public function setField($name, $value)
		{
			$this->data[$name] = $value;

			return $this;
		}

		public function getField($name)
		{
			return $this->data[$name];
		}

		public function hasField($name)
		{
			return array_key_exists($name, $this->data);
		}

		public function getSessionId()
		{
			return $this->sessionId;
		}

		public function commit()
		{
			$this->sessionStorage->save($this->getSessionId(), $this->data()->val(), date('Y-m-d H:i:s'), $this->getSessionLifetimeInSeconds());
			setcookie($this->getCookieName(), $this->getSessionId(), strtotime('now') + $this->getSessionLifetimeInSeconds());
			$_COOKIE[$this->getCookieName()] = $this->getSessionId();

			return $this;
		}

		public function getSessionLifetimeInSeconds()
		{
			return $this->sessionLifetimeInSeconds;
		}

		public function getCookieName()
		{
			return $this->cookieName;
		}

		/** @return \pficl\Web\Session */
		public static function make(\pficl\Cache\IStorage $storage, $sessionLifetimeInSeconds, $cookieName)
		{
			return new static($storage, $sessionLifetimeInSeconds, $cookieName);
		}
	}
}

?>
