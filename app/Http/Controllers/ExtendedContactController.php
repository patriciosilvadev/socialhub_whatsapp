<?php

namespace App\Http\Controllers;

use App\Business\FileUtils;
use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Repositories\ExtendedAttendantsContactRepository;
use App\Repositories\ExtendedContactRepository;
use Auth;
use Flash;
use Illuminate\Http\Request;
use Response;

class ExtendedContactController extends ContactController
{

    public function __construct(ExtendedContactRepository $contactRepo)
    {
        parent::__construct($contactRepo);

        $this->contactRepository = $contactRepo;
        $this->APP_FILE_PATH = env('APP_FILE_PATH');
    }

    /**
     * Display a listing of the Contact.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $User = Auth::check() ? Auth::user() : session('logged_user');
            if($User){
                $Contacts = $this->contactRepository->all();
                if ($User->role_id == ExtendedContactsStatusController::MANAGER) {
                    $Contacts = $this->contactRepository->fullContacts((int) $User->company_id, null);
                } else if ($User->role_id == ExtendedContactsStatusController::ATTENDANT) {
                    $filter = $request->filter_contact;
                    $Contacts = $this->contactRepository->fullContacts((int) $User->company_id, (int) $User->id, $filter);
                }    
                return $Contacts->toJson();
            }else{
                //emitir mensagem de erro, sessão morreu
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created Contact in storage.
     *
     * @param CreateContactRequest $request
     *
     * @return Response
     */
    public function store(CreateContactRequest $request)
    {
        $input = $request->all();

        $User = Auth::check() ? Auth::user() : session('logged_user');
        $input['company_id'] = $User->company_id;

        $contact = $this->contactRepository->create($input);

        Flash::success('Contact saved successfully.');

        return $contact->toJson();
        //return redirect(route('contacts.index'));
    }

    public function contactsFromCSV(CreateContactRequest $request)
    {
        $input = $request->all();
        $User = Auth::check() ? Auth::user() : session('logged_user');

        if ($file = $request->file('file')) {

            $csv = file_get_contents($file->getRealPath());
            $array = array_map("str_getcsv", explode("\n", $csv));
            $json = json_encode($array);
            unlink($file->getRealPath());
            
            //insert contacts in database
            foreach($array as $contact){
                try{
                    // $name = $contact[0];
                    // $name = trim($name);
                    $whatsapp = $contact[1];
                    $whatsapp= trim(str_replace('/', '', str_replace(' ', '', str_replace('-', '', str_replace(')', '', str_replace('(', '', $whatsapp))))));

                    $Contact = new Contact();
                    $Contact->company_id = $User->company_id;
                    $Contact->origin = 3;
                    
                    if (preg_match("/^[a-z A-Z0-9çÇáÁéÉíÍóÓúÚàÀèÈìÌòÒùÙãÃõÕâÂêÊôÔûÛñ\._-]{2,150}$/" , $contact[0])) {
                        $Contact->first_name = trim($contact[0]);
                    }
                    if (preg_match("/^[0-9]{1,3}\ ?[0-9]{1,3}\ ?[0-9]{3,5}(?:-)?[0-9]{4}$/", $whatsapp) ) {
                        $Contact->whatsapp_id = $whatsapp;
                    }
                    if ($contact[2] && filter_var(trim($contact[2]), FILTER_VALIDATE_EMAIL)) {
                        $Contact->email = trim($contact[2]);
                    }

                    if ($contact[3] && preg_match("/^[a-zA-Z0-9\._]{1,300}$/" , $contact[3])) {
                        $Contact->facebook_id = trim($contact[3]);
                    }
                    if ($contact[4] && preg_match("/^[a-zA-Z0-9\._]{1,300}$/" , $contact[4])) {
                        $Contact->instagram_id = trim($contact[4]);
                    }
                    if ($contact[5] && preg_match("/^[a-zA-Z0-9\._]{1,300}$/" , $contact[5])) {
                        $Contact->linkedin_id = trim($contact[5]);
                    }
                    if ($contact[6] && preg_match("/^[a-z A-Z0-9çÇáÁéÉíÍóÓúÚàÀèÈìÌòÒùÙãÃõÕâÂêÊôÔûÛñ\.,_-]{2,80}$/" , $contact[6])) {
                        $Contact->estado = trim($contact[6]);
                    }
                    if ($contact[7] && preg_match("/^[a-z A-Z0-9çÇáÁéÉíÍóÓúÚàÀèÈìÌòÒùÙãÃõÕâÂêÊôÔûÛñ\.,_-]{2,80}$/" , $contact[7])) {
                        $Contact->cidade = trim($contact[7]);
                    }
                    if ($contact[8] && preg_match("/^[a-z A-Z0-9çÇáÁéÉíÍóÓúÚàÀèÈìÌòÒùÙãÃõÕâÂêÊôÔûÛñ\.,_-]{2,80}$/" , $contact[8])) {
                        $Contact->categoria1 = trim($contact[8]);
                    }
                    if ($contact[9] && preg_match("/^[a-z A-Z0-9çÇáÁéÉíÍóÓúÚàÀèÈìÌòÒùÙãÃõÕâÂêÊôÔûÛñ\.,_-]{2,80}$/" , $contact[9])) {
                        $Contact->categoria2 = trim($contact[9]);
                    }

                    if(!empty($Contact->first_name) && !empty($Contact->whatsapp_id)){
                        $Contact->save();
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
            
        } else {
            abort(302, "Error uploading file!");
        }





        // $User = Auth::check() ? Auth::user() : session('logged_user');
        // $input['company_id'] = $User->company_id;

        // $contact = $this->contactRepository->create($input);

        // Flash::success('Contact saved successfully.');

        // return $contact->toJson();
        //return redirect(route('contacts.index'));
    }

    /**
     * Update the specified Contact in storage.
     *
     * @param  int              $id
     * @param UpdateContactRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateContactRequest $request)
    {
        $input = $request->all();

        $contact = $this->contactRepository->findWithoutFail($id);

        if (empty($contact)) {
            Flash::error('Contact not found');
            return redirect(route('contacts.index'));
        }

        $contact = $this->contactRepository->update($input, $id);

        Flash::success('Contact updated successfully.');

        // return redirect(route('contacts.index'));
        return $contact->toJson();
    }

    /**
     * Update the specified Contact in storage.
     *
     * @param  int              $id
     * @param UpdateContactRequest $request
     *
     * @return Response
     */
    public function updatePicture($id, Request $request)
    {
        $Contact = Contact::find($id);

        $Controller = new ExternalRPIController(null);
        $contactInfo = $Controller->getContactInfo($Contact->whatsapp_id);
        $Contact->json_data = $contactInfo;
        $Contact->timestamps = false;
        $Contact->save();

        if (empty($Contact)) {
            Flash::error('Contact not found');
            return redirect(route('contacts.index'));
        }

        Flash::success('Contact picture updated.');

        // return redirect(route('contacts.index'));
        return $Contact->toJson();
    }

    /**
     * Remove the specified Contact from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $contact = $this->contactRepository->findWithoutFail($id);

        if (empty($contact)) {
            Flash::error('Contact not found');

            return redirect(route('contacts.index'));
        }

        // Delete All Attendants x Contact from attendats_contacs table
        // app('PrintReportController::class')->deleteAllByContactId($id);

        $extAttContRepo = new ExtendedAttendantsContactRepository(app());

        $extAttContRepo->deleteAllByContanct($id);

        $this->contactRepository->delete($id);

        Flash::success('Contact deleted successfully.');

        // return redirect(route('contacts.index'));
    }


}
