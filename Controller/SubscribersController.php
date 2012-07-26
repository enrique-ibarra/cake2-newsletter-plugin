 <?php
/**
 *
 * Roadbees subscriber Controller
 *
 *
 * @copyright Copyright (c) 2011, Haithem Bel Haj
 * @author Haithem Bel Haj <haythem.belhaj@googlemail.com>
 * @link          http://roadbees.de
 * @since         Roadbees v 0.1
 */

class SubscribersController extends AppController {



	public $name = 'Subscribers';

	var $uses = array('Newsletter.Subscriber','Newsletter.Campaign');

	/**
	 *
	 * subscribe to newsletter
	 *
	 * @public
	 */

	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow();
	}
	
	public function subscribe(){
		if($this->data){
			$this->Subscriber->Behaviors->attach('Mongodb.SqlCompatible');
			$this->Subscriber->create();
			$this->data['Subscriber']['email'] = str_replace(" ", "", $this->data['Subscriber']['email']);
			$this->Subscriber->set($this->data);
			if($this->Subscriber->validates()) {
				if($this->Subscriber->save()){
					$this->Session->setFlash("Email angelegt");
					$this->redirect(array('manager' => false, 'controller' => "newsletters", "action" => "index"));	
				}else {			
					$this->Session->setFlash("Email konnte nicht angelegt werden");
					$this->render();
				}
			}
		}
	}
        
	
	/**
	 *
	 * unsubscribe to newsletter the website way
	 *
	 * @public
	 */
	
	public function unsubscribe(){
		if($this->data) {
			$subsriber = $this->Subsriber->find('first',array('conditions' => array('Subsriber.email' => $this->data['email'])));
			if($subsriber){
				unsuscribe($subsriber['Subsriber']['_id']);
			}else{
				$this->Session->setFlash(__d('email','Die Email wurde nicht gefunden',false));
			    $this->redirect('/newsletters/unsubscribe');
			}
		}
	}
	
	/**
	 *
	 * unsubscribe to newsletter the email way
	 *
	 * @public
	 */
	
	public function unsubscribeId($id = null){
		if($this->Subsriber->delete($id)){
			$this->Session->setFlash(__d('email','Email wurde gelöscht',false));
			$this->redirect('/newsletters');
		}else{
			$this->Session->setFlash(__d('email','Probleme beim Löschen ',false));
			$this->redirect('/newsletters');
		}
	}

	/* MANAGER FUNCTIONS */
        


	/**
	* manager_subscriber_index
	*
	*/

	public function manager_index() {
		$subscribers = $this->Subscriber->find('all');
		$this->set('subscribers' , $subscribers);
	}

	/**
	* manager_subscriber_view
	*
	*/

	public function manager_view($id = null) {
		$this->Subscriber->id = $id;
		$this->set('subscriber', $this->Subscriber->read());
	}


	/**
	* manager_subscriber_edit
	*
	*/
	
	public function manager_edit($id = null) {
		$this->Subscriber->id = $id;
		
		if ($this->request->is('get')) {
			$this->set('campaigns', $this->Campaign->find('all',array('fields' => 'name')));
			$this->request->data = $this->Subscriber->read();
			
		 } else {
			if ($this->Subscriber->save($this->request->data)) {
				
				$this->Session->setFlash("Subscriber angelegt");
				$this->redirect(array('manager' => true, 'controller' => "subscribers", "action" => "index"));
			} else {
				$this->Session->setFlash("Subscriber konnte nicht angelegt werden");
				$this->render();
			}
		}		
		
	}
}
