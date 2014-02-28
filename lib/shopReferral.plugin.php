<?php

class shopReferralPlugin extends shopPlugin {

    public function backendMenu() {
        if ($this->getSettings('status') || 1) {
            $html = '<li ' . (waRequest::get('plugin') == $this->id ? 'class="selected"' : 'class="no-tab"') . '>
                        <a href="?plugin=referral">Партнерская программа</a>
                    </li>';
            return array('core_li' => $html);
        }
    }

    public function frontendMy() {
        $url = wa()->getAppUrl(null, false) . 'my/referral/';
        $html = '<a href="' . $url . '">Партнерская программа</a>';
        return $html;
    }

    public function frontendHead() {
/*
        $referral_id = waRequest::get('referral_id', 0);
        if ($referral_id) {
            $this->setReferralId($referral_id);
        }
*/
        
        $contact = wa()->getUser();
        echo $contact->get('referral_id', "default");
        //echo $this->getReferralId();

        //$session->write('referral',1);
        //echo $session->read('referral');
        /*
          if (wa()->getUser()->isAuth()) {
          $contact = wa()->getUser();
          } else {
          $contact = new waContact();
          }

          echo $contact->get('referral_id');


          $contact->save(); */
    }

    protected function setReferralId($referral_id) {
        if (wa()->getUser()->isAuth()) {
            $contact = wa()->getUser();
            $contact->set('referral_id', $referral_id);
            $contact->save();
        } else {
            $session = wa()->getStorage();
            $session->write('referral_id', $referral_id);
        }
    }

    protected function getReferralId() {
        $session = wa()->getStorage();
        if (wa()->getUser()->isAuth()) {
            $contact = wa()->getUser();
            if ($contact->get('referral_id')) {
                $referral_id = $contact->get('referral_id');
            } else {
                $referral_id = $session->read('referral_id');
                //$this->setReferralId($referral_id);
            }
        } else {
            $referral_id = $session->read('referral_id');
        }
        return $referral_id;
    }

}
