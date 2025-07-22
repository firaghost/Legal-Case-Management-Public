<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CaseFile;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Show the form for creating a new appointment.
     */
    public function create(CaseFile $case)
    {
        $this->authorize('update', $case);
        return view('lawyer.cases.appointments.create', [
            'case' => $case
        ]);
    }
    /**
     * List appointments for a case file.
     */
    public function index(CaseFile $caseFile)
    {
        $this->authorize('view', $caseFile);
        return $caseFile->appointments()->latest('appointment_date')->get();
    }

    /**
     * Store a new appointment.
     */
    public function store(Request $request, CaseFile $case)
    {
        $this->authorize('update', $case);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Combine date and time
        $appointmentDate = \Carbon\Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_time']);
        
        $appointment = new Appointment([
            'title' => $validated['title'],
            'purpose' => $validated['purpose'],
            'appointment_date' => $appointmentDate->format('Y-m-d'),
            'appointment_time' => \Carbon\Carbon::createFromFormat('H:i', $validated['appointment_time'])->format('H:i:s'),
            'location' => $validated['location'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'case_file_id' => $case->id,
            'created_by' => $request->user()->id,
        ]);
        
        $appointment->save();

        return redirect()
            ->route('lawyer.cases.show', $case)
            ->with('success', 'Appointment created successfully.');
    }

    /**
     * Update appointment.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment->caseFile);
        $data = $request->validate([
            'appointment_date' => 'sometimes|date',
            'purpose' => 'sometimes|string',
            'notes' => 'nullable|string',
        ]);
        $appointment->update($data);
        return response()->json($appointment);
    }

    /**
     * Delete appointment.
     */
    public function destroy(Appointment $appointment)
    {
        $this->authorize('update', $appointment->caseFile);
        $appointment->delete();
        return response()->noContent();
    }
}






