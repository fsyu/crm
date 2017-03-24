<?php
//C:\AppServ\www\cca\application\modules\exam\forms\Generic.php

class English_Form_Generic extends ZendX_JQuery_Form{ 
	public $genericDecorators = 
		array(
			'ViewHelper',
			'Errors',
			'Description',
			// for inner tags using 'Value' placement
			array(array('Value'=>'HtmlTag'),array('tag' => 'div', 'id' => 'select_parent_div')),
			array('HtmlTag',array('tag' => 'td')), //for parallel's placement using two <td>
			array('Label',array('tag' => 'td','class' =>'element')),
			array(array('Row' => 'HtmlTag'), array('tag' => 'tr'))
		);
	public $simpleDecorators = 
		array(
			'ViewHelper',
			'Errors',
			'Description',
			array('HtmlTag',array('tag' => 'td')), //for parallel's placement using two <td>
			array(array('Row' => 'HtmlTag'), array('tag' => 'tr'))
		);
	public $formJQueryElements = array(
			array('UiWidgetElement', array('tag' => '')), // it necessary to include for jquery elements
			array('Errors'),
			array('Description', array('tag' => 'span')),
			array('HtmlTag', array('tag' => 'td')),
			array('Label', array('tag' => 'td', 'class' =>'element')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);
	public $formJQueryElements2 = array(
			array('UiWidgetElement', array('tag' => '')), // it necessary to include for jquery elements
			array('Errors'),
			array('Description', array('tag' => 'span')),
			array('HtmlTag', array('tag' => 'td')),
			array('Label', array('tag' => 'td', 'class' =>'element')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);
	public function init() {
	    $this->setAction('#');
	    $this->setMethod('post');
	
/*
	    $this->addElement('text', 'exampart__exampart_id', array(
	    'decorators' => $this->elementDecorators,
	    'filters'    => array('StringTrim'),
	    'validators' => array(
	        array('StringLength', true, 
	        ),
	    ),
	    'required'   => false,
	    'size'   => 10,
	    'label'      => EXAMPART_EXAMPART_ID,
		));

	    $this->addElement('text', 'exampart__name', array(
	    'decorators' => $this->elementDecorators,
	    'filters'    => array('StringTrim'),
	    'validators' => array(
	        array('StringLength', true, 
	        ),
	    ),
	    'required'   => false,
	    'size'   => 45,
	    'label'      => EXAMPART_NAME,
		));
		
		$elem = new ZendX_JQuery_Form_Element_DatePicker('exampart__year', array(
	    'decorators' => $this->formJQueryElements,
		'label' => EXAMPART_YEAR,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

	    $this->addElement('text', 'campaign__enrollment', array(
	    'decorators' => $this->elementDecorators,
	    'filters'    => array('StringTrim'),
	    'validators' => array(
	        array('StringLength', true, 
	        ),
	    ),
	    'required'   => false,
	    'size'   => 10,
	    'label'      => CAMPAIGN_ENROLLMENT,
		));

	    $this->addElement('text', 'campaign__finalist', array(
	    'decorators' => $this->elementDecorators,
	    'filters'    => array('StringTrim'),
	    'validators' => array(
	        array('StringLength', true, 
	        ),
	    ),
	    'required'   => false,
	    'size'   => 10,
	    'label'      => CAMPAIGN_FINALIST,
		));

	    $this->addElement('text', 'campaign__winner', array(
	    'decorators' => $this->elementDecorators,
	    'filters'    => array('StringTrim'),
	    'validators' => array(
	        array('StringLength', true, 
	        ),
	    ),
	    'required'   => false,
	    'size'   => 10,
	    'label'      => CAMPAIGN_WINNER,
		));

		$elem = new ZendX_JQuery_Form_Element_DatePicker('campaign__submit', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_SUBMIT,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

		$elem = new ZendX_JQuery_Form_Element_DatePicker('campaign__due', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_DUE,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

		$elem = new ZendX_JQuery_Form_Element_DatePicker('campaign__accept', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_ACCEPT,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

		$elem = new ZendX_JQuery_Form_Element_DatePicker('campaign__published', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_PUBLISHED,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

		$elem = new ZendX_JQuery_Form_Element_DatePicker('campaign__created', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_CREATED,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);
*/
	/*
// Email
	        $this->addElement('text', 'email', array(
	            'decorators' => $this->elementDecorators,
	            'label'       => 'Email:',
	            'required'   => true,
	            'validators'  => array(
	                            'EmailAddress',
	                            ),
	            'attribs' =>   array(
	                                'id'=>'email_id',
	                            'class'=>'email_class'
	                            ),
	        ));
//Combox 
	        $this->addElement('select', 'country', array(
	            'decorators' => $this->elementDecorators,
	            'label'      => 'Country:',
	            'required'   => true,
	            'attribs' =>   array(
								'id'=>'country_id',
	                            ),
	            'multioptions'   => array(
	                            'ph' => 'Philippines',
	                            'us' => 'USA',
	                            ),
	        ));
//Radio button
	        $this->addElement('radio', 'gender', array(
	            'decorators' => $this->elementDecorators,
	            'label'      => 'Gender:',
	            'required'   => true,
	            'attribs' =>   array(
	                                'id'=>'gender_id',
	                            ),
	            'multioptions'   => array(
	                            'male' => 'Male',
	                            'female' => 'Female',
	                            ),
	        ));
//Checkbox 
	        $checkboxDecorator = 
				array(
				'ViewHelper',
				'Errors',
				array(array('data' => 'HtmlTag'), array('tag' => 'span', 'class' => 'element')),
				array('Label', array('tag' => 'dt'),
				array(array('row' => 'HtmlTag'), array('tag' => 'span')),
	                            ));
	        $this->addElement('checkbox', 'agreement', array(
	            'decorators' => $checkboxDecorator,
	            'label'       => 'Agreement:',
	            'required'   => true,
	        ));
	*/

	}
	public function loadDefaultDecorators(){ 

	    $this->setDecorators(array(
	        'FormElements',
	        array('HtmlTag', array('tag' => 'table')),
	        'Form',
	        'Errors'
	    ));
	}

}