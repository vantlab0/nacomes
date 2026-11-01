<?php

namespace App\Http\Requests;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class VoteSubmitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasVerifiedEmail();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'election_id' => ['required', 'integer', 'exists:elections,id'],
            'votes' => ['required', 'array', 'min:1'],
            'votes.*' => ['required', 'integer', 'exists:candidates,id'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $electionId = (int) $this->input('election_id');
            $election = Election::find($electionId);

            if (! $election) {
                $validator->errors()->add('election_id', 'The specified election was not found.');

                return;
            }

            if (! $election->isOpen()) {
                $validator->errors()->add('election_id', 'This election is currently closed or not yet open for voting.');

                return;
            }

            $votes = (array) $this->input('votes');

            foreach ($votes as $positionId => $candidateId) {
                $position = Position::where('id', $positionId)
                    ->where('election_id', $election->id)
                    ->first();

                if (! $position || ! $position->is_published) {
                    $validator->errors()->add("votes.{$positionId}", 'Invalid or unpublished position selected.');

                    continue;
                }

                $candidate = Candidate::where('id', $candidateId)
                    ->where('position_id', $position->id)
                    ->where('election_id', $election->id)
                    ->first();

                if (! $candidate || ! $candidate->is_published) {
                    $validator->errors()->add("votes.{$positionId}", 'Selected candidate is not eligible or not assigned to this position.');
                }
            }
        });
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'votes.required' => 'Please select at least one candidate on your ballot.',
            'votes.min' => 'You must make at least one candidate selection before submitting.',
        ];
    }
}
