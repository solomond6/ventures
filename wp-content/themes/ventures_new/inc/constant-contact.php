<?php 

define('CTCT_SRC_DIR', realpath(__DIR__.'/vendor/constantcontact'));
define('CTCT_API_KEY', 'csfg2yy7u8uj5k8guz3m8w77');
define('CTCT_SECRET', 'GmvWFHrDz7Gmb4dbk9uQuwbP');
define('CTCT_ACCESS_TOKEN', '0d91a50e-9ee5-465f-b347-da23c9b20bc3');
define('CTCT_VA_LIST_ID', '2');

add_action('wp_ajax_nopriv_ventures_mailinglist_join', 'ventures_mailinglist_join');
add_action('wp_ajax_ventures_mailinglist_join', 'ventures_mailinglist_join');
function ventures_mailinglist_join()
{
    $email = stripslashes($_REQUEST['ea']);
    echo ventures_mailinglist_cc_subscribe($email);
    exit;
}

function ventures_mailinglist_cc_subscribe($email) {
    require_once(CTCT_SRC_DIR . '/src/Ctct/autoload.php');

    $cc = new Ctct\ConstantContact(CTCT_API_KEY);

    try {
        if (!empty($email)) {
            $response = $cc->getContactByEmail(CTCT_ACCESS_TOKEN, $email);
            if ($response->results) {
                return 'You are already subscribed to our mailing list.';
            }
        }

        $contact = new Ctct\Components\Contacts\Contact();
        $contact->addEmail($email);
        $contact->addList(CTCT_VA_LIST_ID);
        /**
         * The third parameter of addContact defaults to false, but if this were 
         * set to true it would tell Constant Contact that this action is being 
         * performed by the contact themselves, and gives the ability to
         * opt contacts back in and trigger Welcome/Change-of-interest emails.
         *
         * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
         */
        $returnContact = $cc->addContact(CTCT_ACCESS_TOKEN, $contact, true);
        return 'Thank you! You have been subscribed.';
    } 
    catch (Ctct\Exceptions\CtctException $e) {
        $errors = [];
        foreach($e->getErrors() as $err) {
            $errors[] = trim(str_replace('Value ','',array_pop(explode('email_address: ', $err['error_message']))), ' .');
        }
        $error_str = implode(' and ', $errors);
        return 'The address you entered ('.$email.') '.$error_str.'.';
    }
}

