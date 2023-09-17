<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Enums\AppointmentStatus;

class AppointmentController extends Controller
{
    public function index()
    {
        return Appointment::query()
            ->with('client:id,first_name,last_name')
            ->when(
                request('status'),
                function ($query) {
                    $query->where('status', AppointmentStatus::from(request('status')));
                }
            )
            ->latest()
            ->paginate()
            ->through(fn ($appointment) => [
                'id' => $appointment->id,
                'client' => $appointment->client,
                'created_at' => $appointment->created_at->format('d/m/Y à H:i'),
                'updated_at' => $appointment->updated_at->format('d/m/Y à H:i'),
                'status' => [
                    'name' => $appointment->status->name,
                    'color' => $appointment->status->color(),
                ],
            ]);
    }
    public function store()
    {
        $validated = request()->validate([
            'client_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'created_at' => 'required',
            'updated_at' => 'required',
        ], [
            'client_id.required' => 'The client name field is required.',
        ]);

        Appointment::create([
            'title' => $validated['title'],
            'client_id' => $validated['client_id'],
            'created_at' => $validated['created_at'],
            'updated_at' => $validated['updated_at'],
            'description' => $validated['description'],
            'status' => AppointmentStatus::SCHEDULED,
        ]);

        return response()->json(['message' => 'success']);
    }

    public function edit(Appointment $appointment)
    {
        return $appointment;
    }

    public function update(Appointment $appointment)
    {
        $validated = request()->validate([
            'client_id' => 'required',
            'title' => 'required',
            'description' => 'required',
            'created_at' => 'required',
            'updated_at' => 'required',
        ], [
            'client_id.required' => 'The client name field is required.',
        ]);

        $appointment->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json(['success' => true], 200);
    }
}
