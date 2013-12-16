<?php

namespace pficl\Core
{
	final class TemplateData
	{
		private $template;
		private $data;

		/** @return \pficl\Core\TemplateData */
		public static function make(Template $tpl, array $data)
		{
			return new self($tpl, $data);
		}

		private function __construct(Template $tpl, array $data)
		{
			$this->template = $tpl;
			$this->data = \pficl\Collection\SafeAccess::make($data);
		}

		public function tpl()
		{
			return $this->template;
		}

		/** @return \pficl\Collection\SafeAccess */
		public function data()
		{
			return $this->data;
		}
	}
}

?>
