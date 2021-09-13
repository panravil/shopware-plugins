<?php 

class Shopware_Controllers_Backend_GetProductStream extends Shopware_Controllers_Backend_ExtJs
{
	public function index()
    {
        $data = [
            ['id' => 1, 'description' => 'foo'],
            ['id' => 2, 'description' => 'bar'],
        ];
    
        $this->view->assign([
            'data' => $data,
            'total' => count($data),
        ]);
    }
}

?>