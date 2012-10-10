<?php
/**
 * App Controller
 *
 */
class AppController extends Controller {
	
	public $helpers = array('Html','Form','Session','Js','Renderer');//helpers empleados
	public $components = array('RequestHandler','Session');//componentes empleados
	
}