<?php

namespace core\utils\displayMessages;

class DisplayMessageFactory
{

	private static $instance = null;
	private $messages = [];

	public static $INFO = 'info';
	public static $SUCCESS = 'success';
	public static $WARNING = 'warning';
	public static $DANGER = 'danger';

	public function __construct()
	{
		if (isset($_SESSION['xw_display_messages'])) {
			$this->messages = $_SESSION['xw_display_messages'];
		}
	}

	public static function instance()
	{
		if (self::$instance == null) {
			self::$instance = new DisplayMessageFactory();
		}
		return self::$instance;
	}

	private function persist()
	{
		$_SESSION['xw_display_messages'] = $this->messages;
	}

	public function addDisplayMessage($title, $message = '', $type = 'info')
	{
		$msg = [
			'title' => $title,
			'message' => $message,
			'type' => in_array(strtolower($type), ['info', 'warning', 'success', 'danger']) ? strtolower($type) : 'info'
		];

		$this->messages[] = $msg;
		$this->persist();
	}

	public function clear()
	{
		$this->messages = [];
		$this->persist();
	}

	public function renderAll()
	{
		foreach ($this->messages as $msg) {
			?>
			<div class="panel displaymessage displaymessage-<?= $msg['type'] ?>">
				<div class="panel-body bg-info ">
					<strong><?= $msg['title'] ?></strong><br>
					<?= $msg['message'] ?>
				</div>
			</div>
<?php
		}
		$this->clear();
	}

	public function getAllAndClear()
	{
		$all = $this->messages;
		$this->clear();
		return $all;
	}
}
