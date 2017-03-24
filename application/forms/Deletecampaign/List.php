<?php
//C:\AppServ\www\cca\application\modules\campaign\forms\Deletecampaign.php

class Campaign_Form_Deletecampaign extends ZendX_JQuery_Form{ 

	public $checkboxDecorator =  
		array(
			'ViewHelper',
			'Errors',
			'Description',
			array('HtmlTag',array('tag' => 'td')),
			array('Label',array('tag' => 'td','class' =>'element')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr')));
	public $elementDecorators = 
		array(
			'ViewHelper',
			'Errors',
			'Description',
			array('HtmlTag',array('tag' => 'td')),
			array('Label',array('tag' => 'td','class' =>'element')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr')));
	public $buttonDecorators = 
		array(
			'ViewHelper',
			array('HtmlTag',array('tag' => 'td')),
			//array('Label',array('tag' => 'td')), NO LABELS FOR BUTTONS
			array(array('row' => 'HtmlTag'), array('tag' => 'tr')));
	public $formJQueryElements = array(
			array('UiWidgetElement', array('tag' => '')), // it necessary to include for jquery elements
			array('Errors'),
			array('Description', array('tag' => 'span')),
			array('HtmlTag', array('tag' => 'td')),
			array('Label', array('tag' => 'td', 'class' =>'element')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
	);
	public function init()
	{
	    $this->setAction('');
	    $this->setMethod('post');
	
	    $this->addElement('text', 'campaign_id', array(
	    'decorators' => $this->elementDecorators,
	    'filters'    => array('StringTrim'),
	    'validators' => array(
	        array('StringLength', true, 
	        ),
	    ),
	    'required'   => false,
	    'size'   => 10,
	    'label'      => CAMPAIGN_CAMPAIGN_ID,
		));

	    $this->addElement('text', 'name', array(
	    'decorators' => $this->elementDecorators,
	    'filters'    => array('StringTrim'),
	    'validators' => array(
	        array('StringLength', true, 
	        ),
	    ),
	    'required'   => false,
	    'size'   => 45,
	    'label'      => CAMPAIGN_NAME,
		));

		$elem = new ZendX_JQuery_Form_Element_DatePicker('year', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_YEAR,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

	    $this->addElement('text', 'enrollment', array(
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

	    $this->addElement('text', 'finalist', array(
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

	    $this->addElement('text', 'winner', array(
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

		$elem = new ZendX_JQuery_Form_Element_DatePicker('submit', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_SUBMIT,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

		$elem = new ZendX_JQuery_Form_Element_DatePicker('due', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_DUE,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

		$elem = new ZendX_JQuery_Form_Element_DatePicker('accept', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_ACCEPT,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

		$elem = new ZendX_JQuery_Form_Element_DatePicker('published', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_PUBLISHED,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

		$elem = new ZendX_JQuery_Form_Element_DatePicker('created', array(
	    'decorators' => $this->formJQueryElements,
		'label' => CAMPAIGN_CREATED,
		'required' => false,
		'validators' => array('Date'),
		'jQueryParams' => array(
		'dateFormat' => 'yy-mm-dd',
		)));
		$this->addElement($elem);

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
	        $this->addElement('submit', 'formsubmit', array(
	    'decorators' => $this->buttonDecorators,
	    'label'       => 'OK',
	));
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