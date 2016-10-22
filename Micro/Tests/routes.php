<?php
$Router
->notSafe()
->controllerSpace('MicroModules\Example\Controllers')
->nameSpace('111111')


->End_nameSpace()
->nameSpace('222222')
	->node('/blog')

->route('/', 'ShopController')								->name('micro')
							->get('show') 		
  	 						->put('putAction')
  	 					 ->delete('deleteAction')

->node('/cms2')

->route('/{?id}', 'ShopController')		->name('adfa')
  ->get('show')
->regex([
			'id'  => '\d',
		])

->End_node()#cms

		// ->includeFile(__DIR__.'/modules/Example/routes.php')



	->End_node()#blog
->End_nameSpace()
	
// ->list('/microroutes')


;# $Router ....................................................................
// \d::p($this);