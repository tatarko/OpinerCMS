<?php

/**
 * Article controller
 * @author Tomas Tatarko <tomas@tatarko.sk>
 */
class Article extends Fertu\Controller {

	public function actionView($id) {

		exit('aa' . $id);
		$this->render('view', array('message' => 'Hello world'));
	}
}