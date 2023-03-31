<?php namespace App\SupportedApps\UptimeKumaEnhanced;

class UptimeKumaEnhanced extends \App\SupportedApps implements \App\EnhancedApps
{
	public $config;

	function __construct()
	{
	}

	public function test()
	{
		$test = parent::appTest($this->url("/status-page/heartbeat/default"), $this->getAttrs());
		echo $test->status;
	}

	public function livestats()
	{
		$status = "active";
		$res = parent::execute($this->url("/status-page/heartbeat/default"), $this->getAttrs());
		$heartbeat_data = json_decode($res->getBody());
        $uptime_list = $heartbeat_data->uptimeList;

		$checks_total = 0;
		$checks_up = 0;

		foreach ($uptime_list as $key => $value) {
			$checks_total++;
			$checks_up += $value;
		}

        $uptime_status = "good";
        if ($checks_total > $checks_up) {
            $uptime_status = "bad";
        }

		if ($checks_total > 0) {
			$status = "active";
		}

		$data = [];
		$data["checks_total"] = $checks_total;
		$data["checks_up"] = $checks_up;
        $data["uptime_status"] = $uptime_status;
		return parent::getLiveStats($status, $data);
	}

	public function url($endpoint)
	{
		$api_url = parent::normaliseurl($this->config->url) . "api" . $endpoint;
		return $api_url;
	}

	private function getAttrs()
	{
		return [];
	}
}

