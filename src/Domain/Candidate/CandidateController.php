<?php

namespace App\Domain\Candidate;

use App\Http\Request;
use App\Http\Response;

class CandidateController
{
    private CandidateRepository $repo;

    public function __construct(?CandidateRepository $repo = null)
    {
        $this->repo = $repo ?? new CandidateRepository();
    }

    public function apiIndex(Request $request): void
    {
        $filters = [
            'election_id' => $request->query('election_id'),
            'year' => $request->query('year'),
            'state_code' => $request->query('state_code'),
            'office' => $request->query('office'),
            'party' => $request->query('party'),
            'status' => $request->query('status'),
            'ballot_number' => $request->query('ballot_number'),
            'name' => $request->query('name'),
            'page' => $request->query('page', 1),
            'per_page' => $request->query('per_page', 20),
            'sort' => $request->query('sort', 'ballot_name')
        ];

        $result = $this->repo->findPaginated($filters);
        Response::success($result['data'], $result['meta']);
    }

    public function apiShow(Request $request, string $id): void
    {
        $candidate = $this->repo->findById((int)$id);
        if (!$candidate) {
            Response::error('CANDIDATE_NOT_FOUND', 'Candidato não encontrado.', [], 404);
            return;
        }

        $sources = $this->repo->findSources((int)$id);
        $proposals = $this->repo->findProposals((int)$id);
        $records = $this->repo->findRecords((int)$id);

        $candidate['sources'] = $sources;
        $candidate['proposals'] = $proposals;
        $candidate['records'] = $records;

        Response::success($candidate);
    }

    public function apiSources(Request $request, string $id): void
    {
        $candidate = $this->repo->findById((int)$id);
        if (!$candidate) {
            Response::error('CANDIDATE_NOT_FOUND', 'Candidato não encontrado.', [], 404);
            return;
        }

        $sources = $this->repo->findSources((int)$id);
        Response::success($sources);
    }

    public function apiProposals(Request $request, string $id): void
    {
        $candidate = $this->repo->findById((int)$id);
        if (!$candidate) {
            Response::error('CANDIDATE_NOT_FOUND', 'Candidato não encontrado.', [], 404);
            return;
        }

        $proposals = $this->repo->findProposals((int)$id);
        Response::success($proposals);
    }

    public function apiRecords(Request $request, string $id): void
    {
        $candidate = $this->repo->findById((int)$id);
        if (!$candidate) {
            Response::error('CANDIDATE_NOT_FOUND', 'Candidato não encontrado.', [], 404);
            return;
        }

        $records = $this->repo->findRecords((int)$id);
        Response::success($records);
    }
}

