<?php
class View_CRUD extends View {
    public $form=null;
    public $grid=null;

    public $grid_class='MVCGrid';
    public $form_class='MVCForm';

	public $allow_add=true;
	public $allow_edit=true;
	public $allow_delete=true;
    
    public $frame_options=null;
    function init(){
        parent::init();


        $this->grid=$this->add($this->grid_class);
        $this->js('reload',$this->grid->js()->reload());

        if($this->allow_add){
            if(isset($_GET[$this->name])){
                $this->api->stickyGET($this->name);

                $this->form=$this->add($this->form_class);
                $_GET['cut_object']=$this->name;

                return;
            }
            $this->add_button = $this->grid->addButton('Add');
            $this->add_button->js('click')->univ()
                ->frameURL('New',$this->api->getDestinationURL(null,array($this->name=>'new')),$this->frame_options);
        }
    }
    function setModel($a,$b=null){
        if($this->form){
            $m=$this->form->setModel($a,$b);

            if(($id=$_GET[$this->name])!='new'){
                $m->loadData($id);
            }

            if($this->form->isSubmitted()){
                $this->form->update();
                $this->form->js(null,$this->js()->trigger('reload'))->univ()->closeDialog()->execute();
            }

            return $m;
        }
        $m=$this->grid->setModel($a,$b);
        if($this->allow_edit){
            $this->grid->addColumn('button','edit');
            if($id=@$_GET[$this->grid->name.'_edit']){
                $this->js()->univ()->frameURL('New',$this->api->getDestinationURL(null,array($this->name=>$id)))->execute();
            }
        }
        if($this->allow_delete){
            $this->grid->addColumn('delete','delete');
        }
        return $m;

    }
}
