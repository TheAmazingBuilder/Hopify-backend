<?php

namespace App\Modules\Patient\Actions;

use App\Modules\Patient\Models\Patient;
use App\Modules\Patient\Repositories\PatientRepositoryInterface;
use App\Modules\Patient\Events\PatientCreated;
use App\Modules\Patient\DTOs\CreatePatientDTO;
use Illuminate\Support\Str;

class CreatePatientAction
{
    public function __construct(
        protected PatientRepositoryInterface $repository
    ) {}

    /**
     * Exécute la création d'un patient.
     * 
     * @param CreatePatientDTO $dto
     * @return Patient
     */
    public function execute(CreatePatientDTO $dto): Patient 
    {
        $data = $dto->toArray();

        // Génération automatique du Matricule (MRN) si non fourni
        if (empty($data['mrn'])) {
            $data['mrn'] = $this->generateMrn();
        }

        // Création via le Repository (Abstraction de la DB)
        $patient = $this->repository->create($data);
        
        // Dispatch de l'événement domaine
        event(new PatientCreated($patient));
        
        // Enregistrement de l'audit (Sera actif une fois le modèle AuditLog créé)
        // AuditLog::record('patient.created', $patient);
        
        return $patient;
    }

    /**
     * Génère un matricule unique.
     */
    protected function generateMrn(): string
    {
        return 'PAT-' . date('Ym') . '-' . strtoupper(Str::random(4));
    }
}


<?php

namespace App\Modules\Patient\DTOs;

class CreatePatientDTO
{
    public function __construct(
        public string $fname,
        public string $lname,
        public string $dob,
        public string $gender,
        public ?string $blood_type = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $city = null,
        public ?string $address = null,
        public ?string $marital_status = null,
        public ?string $occupation = null,
        public ?string $nationality = null,
        public ?string $mrn = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            fname: $request->validated('fname'),
            lname: $request->validated('lname'),
            dob: $request->validated('dob'),
            gender: $request->validated('gender'),
            blood_type: $request->validated('blood_type'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
            city: $request->validated('city'),
            address: $request->validated('address'),
            marital_status: $request->validated('marital_status'),
            occupation: $request->validated('occupation'),
            nationality: $request->validated('nationality'),
            mrn: $request->validated('mrn'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'fname' => $this->fname,
            'lname' => $this->lname,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'blood_type' => $this->blood_type,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'address' => $this->address,
            'marital_status' => $this->marital_status,
            'occupation' => $this->occupation,
            'nationality' => $this->nationality,
            'mrn' => $this->mrn,
        ]);
    }
}


<?php

namespace App\Modules\Patient\Repositories;

use App\Modules\Patient\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientRepository implements PatientRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Patient::query()
            ->when(isset($filters['search']), fn($q) => $q->search($filters['search']))
            ->when(isset($filters['gender']), fn($q) => $q->where('gender', $filters['gender']))
            ->when(isset($filters['is_deceased']), fn($q) => $q->where('is_deceased', $filters['is_deceased']))
            ->orderBy('lname')
            ->paginate($perPage);
    }

    public function findByUuid(string $uuid): ?Patient
    {
        return Patient::with(['contacts', 'insurances', 'allergies', 'antecedents'])->find($uuid);
    }

    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function update(string $uuid, array $data): bool
    {
        $patient = Patient::find($uuid);
        if (!$patient) return false;
        return $patient->update($data);
    }

    public function delete(string $uuid): bool
    {
        $patient = Patient::find($uuid);
        if (!$patient) return false;
        return $patient->delete();
    }

    public function findByMrn(string $mrn): ?Patient
    {
        return Patient::where('mrn', $mrn)->first();
    }

    public function searchByName(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Patient::where('fname', 'like', "%{$query}%")
            ->orWhere('lname', 'like', "%{$query}%")
            ->orWhere('mrn', 'like', "%{$query}%")
            ->with(['allergies', 'insurances'])
            ->paginate($perPage);
    }

    public function findWithActiveHospitalization(string $uuid): ?Patient
    {
        return Patient::with(['contacts'])->find($uuid); 
    }
}


<?php

namespace App\Modules\Patient\Repositories;

use App\Modules\Patient\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;

interface PatientRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findByUuid(string $uuid): ?Patient;
    public function create(array $data): Patient;
    public function update(string $uuid, array $data): bool;
    public function delete(string $uuid): bool;
    
    public function findByMrn(string $mrn): ?Patient;
    public function searchByName(string $query, int $perPage): LengthAwarePaginator;
    public function findWithActiveHospitalization(string $id): ?Patient;
}
