<?php

require_once 'modules/Module.class.php';

class ModuleTreso extends Module {

	protected function action_index() {
		global $CONF;
		global $AADMIN;


		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/index.phtml");

		$this->view->add_jsfile('libs/underscore.js');
		$this->view->add_jsfile('modules/treso/res/main.js');
		$this->view->add_jsfile('libs/jquery-ui.js');

		$this->view->add_cssfile('modules/treso/view/jquery-ui-1.9.0.custom.min.css');
		$this->view->add_cssfile('modules/treso/view/main.css');

		$fundations = $AADMIN->get_fundations_with_right("TRESO"); $fundations=$fundations['success'];
		$this->view->add_param("fundations", $fundations);
		$fundation_ok = false;
		if ( isset($_GET["fundation"]) ) {
			foreach ($fundations as $key => $fundation) {
				if (isset($_GET["fundation"]) == $fundation["id"]) {
					$fundation_ok = true;
				}
			}
		}
		if ($fundation_ok) {
			if (isset($_GET["day"]) && isset($_GET["month"]) && isset($_GET["year"]) ) {
				$this->view->add_param("day", $_GET["day"]);
				$this->view->add_param("month", $_GET["month"]);
				$this->view->add_param("year", $_GET["year"]);

				if (isset($_GET["day2"]) && isset($_GET["month2"]) && isset($_GET["year2"]) ) {
					$this->view->add_param("day2", $_GET["day2"]);
					$this->view->add_param("month2", $_GET["month2"]);
					$this->view->add_param("year2", $_GET["year2"]);

					$summary = $AADMIN->get_summary_for_accounting_period(
										intval($_GET["day"]), intval($_GET["month"]), intval($_GET["year"]),
										intval($_GET["day2"]),intval($_GET["month2"]),intval($_GET["year2"]));

				}
				else {
					$summary = $AADMIN->get_summary_for_accounting(intval($_GET["day"]), intval($_GET["month"]), intval($_GET["year"]));
				}
			}

			else {
				$now = mktime();
				$this->view->add_param("day", date("d", $now));
				$this->view->add_param("month", date("m", $now));
				$this->view->add_param("year", date("Y", $now));
				$summary = $AADMIN->get_summary_for_accounting(intval(date("Y")), intval(date("m")), intval(date("d")));
			}
			$this->view->add_param("summary", $summary);
		}

	}

	protected function action_download() {
		global $CONF;
		global $AADMIN;
		$this->view->set_template('csv');
		$this->view->set_view($this->get_path_module()."view/stats.phtml");

		if (isset($_GET["day"]) && isset($_GET["month"]) && isset($_GET["year"]) ) {
			$this->view->add_param("day", $_GET["day"]);
			$this->view->add_param("month", $_GET["month"]);
			$this->view->add_param("year", $_GET["year"]);

			if (isset($_GET["day2"]) && isset($_GET["month2"]) && isset($_GET["year2"]) ) {
				$this->view->add_param("day2", $_GET["day2"]);
				$this->view->add_param("month2", $_GET["month2"]);
				$this->view->add_param("year2", $_GET["year2"]);

				$summary = $AADMIN->get_summary_for_accounting_period(
									intval($_GET["day"]), intval($_GET["month"]), intval($_GET["year"]),
									intval($_GET["day2"]),intval($_GET["month2"]),intval($_GET["year2"]));

			}
			else {
				$summary = $AADMIN->get_summary_for_accounting(intval($_GET["day"]), intval($_GET["month"]), intval($_GET["year"]));
			}
		}
		else {
			$now = mktime();
			$this->view->add_param("day", date("d", $now));
			$this->view->add_param("month", date("m", $now));
			$this->view->add_param("year", date("Y", $now));
			$summary = $AADMIN->get_summary_for_accounting(intval(date("Y")), intval(date("m")), intval(date("d")));
		}
		$this->view->add_param("summary", $summary);

	}

}

?>
