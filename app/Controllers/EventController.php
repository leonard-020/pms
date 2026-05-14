<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Core\Validator;
use App\Models\Event;
use App\Services\AuditService;

class EventController extends Controller
{
    public function index(): void
    {
        $page = (int) ($this->request->query('page', 1) ?: 1);
        $search = $this->request->query('search', '');

        $model = new Event();
        $events = $model->paginateWithCreator($page, 15, $search);

        $this->layout('main', 'events/index', [
            'title'  => 'Events',
            'events' => $events,
            'search' => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
            'success'=> Session::flash('success'),
        ]);
    }

    public function create(): void
    {
        $this->layout('main', 'events/create', [
            'title'  => 'Create Event',
            '_token' => CSRF::field(),
            'old'    => Session::flash('old') ?: [],
            'errors' => Session::flash('errors') ?: [],
        ]);
    }

    public function store(): void
    {
        if (!CSRF::check()) {
            Session::flash('error', 'Invalid security token.');
            $this->back();
            return;
        }

        $validator = new Validator($this->request->all(), [
            'title'          => 'required|max:200',
            'description'    => 'nullable|max:2000',
            'event_date'     => 'required|date',
            'start_time'     => 'nullable',
            'end_time'       => 'nullable',
            'location'       => 'nullable|max:255',
        ]);

        if (!$validator->validate()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $this->request->all());
            $this->redirect('/events/create');
            return;
        }

        $data = $this->request->all();
        $data['created_by'] = Session::get('user.id');
        $data['status'] = 'upcoming';

        $model = new Event();
        $model->create($data);

        (new AuditService())->log('create', 'events', "Created event: {$data['title']}");

        Session::flash('success', 'Event created successfully.');
        $this->redirect('/events');
    }
}