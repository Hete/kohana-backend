<?php

Route::set('backend', '<controller>(/<action>)/<key>', array(
    'controller' => 'backend',
    'action' => 'start|stop|kill|status',
    
    
    
))->defaults(
        array(
            'action' => 'status'
            
        )
        );


Route::set('backend_unit', '<controller>(/<action>/<unit>)/<key>', array(
    'controller' => 'backend',
    'action' => 'start|stop|kill|status',
    'unit' => 'mail'
    
    
    
))->defaults(
        array(
            'action' => 'status'
            
        )
        );

?>
