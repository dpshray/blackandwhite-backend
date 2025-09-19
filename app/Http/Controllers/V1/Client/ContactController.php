<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactRequest;
use App\Http\Resources\Contact\ContactCollection;
use App\Models\Contact;
use App\ResponseTrait;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //
    use ResponseTrait;
    function store(ContactRequest $request)
    {
        $validation=$request->validated();
        $contact=Contact::create($validation);
        if(!$contact)
        {
            return $this->apiError('Failed to add contact');
        }
        return $this->apiSuccess('Contact Successful',$contact);
    }
    function view(Request $request)
    {
        $limit = $request->input('limit', 9);
        $contact = Contact::orderBy('created_at', 'desc')->paginate($limit);
        if(!$contact)
        {
            return $this->apiError('No Contact found');
        }
        $contact=new ContactCollection($contact);
        return $this->apiSuccess('contact data',$contact);
    }
    function delete(Contact $contact)
    {
        $contact->delete();
        return $this->apiSuccess('Deleted Successfull');
    }
}
