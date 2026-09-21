<?php

namespace App\Domain\Web;

use App\Database\Connection;
use App\Domain\Candidate\CandidateRepository;
use App\Http\Request;
use App\Http\Response;
use App\Support\View;

class WebController
{
    private CandidateRepository $candidateRepo;

    public function __construct()
    {
        $this->candidateRepo = new CandidateRepository();
    }

    public function home(Request $request): void
    {
        $pdo = Connection::get();
        $offices = $pdo->query("SELECT id, code, name FROM offices ORDER BY id ASC")->fetchAll();
        $lastUpdate = $pdo->query("SELECT MAX(finished_at) AS last_dt FROM data_imports WHERE status IN ('completed', 'partial')")->fetchColumn();

        $html = View::render('home', [
            'offices' => $offices,
            'lastUpdate' => $lastUpdate ?: 'Aguardando primeira importação'
        ]);
        Response::html($html);
    }

    public function candidates(Request $request): void
    {
        $pdo = Connection::get();
        $offices = $pdo->query("SELECT id, code, name, level FROM offices ORDER BY id ASC")->fetchAll();
        $parties = $pdo->query("SELECT acronym, name FROM parties ORDER BY acronym ASC")->fetchAll();

        $filters = [
            'state_code' => $request->query('state_code', 'SP'),
            'office'       => $request->query('office'),
            'party'        => $request->query('party'),
            'status'       => $request->query('status'),
            'name'         => $request->query('name'),
            'ballot_number'=> $request->query('ballot_number'),
            'page'         => $request->query('page', 1),
            'per_page'     => 12,
            'sort'         => $request->query('sort', 'ballot_name')
        ];

        // Se filtrou por um cargo federal nacional e não há state_code explícito, expande para BR
        $officeUpper = strtoupper(trim($filters['office'] ?? ''));
        if (in_array($officeUpper, ['PRESIDENTE'], true) && empty($request->query('state_code'))) {
            $filters['state_code'] = 'ALL';
        }

        $results = $this->candidateRepo->findPaginated($filters);

        $html = View::render('candidates/list', [
            'offices'    => $offices,
            'parties'    => $parties,
            'candidates' => $results['data'],
            'meta' => $results['meta'],
            'filters' => $filters
        ]);
        Response::html($html);
    }

    public function candidateDetail(Request $request, string $id): void
    {
        $candidate = $this->candidateRepo->findById((int)$id);
        if (!$candidate) {
            Response::html(View::render('errors/404', ['message' => 'Candidato não encontrado']), 404);
            return;
        }

        $sources = $this->candidateRepo->findSources((int)$id);
        $proposals = $this->candidateRepo->findProposals((int)$id);
        $records = $this->candidateRepo->findRecords((int)$id);

        $html = View::render('candidates/detail', [
            'candidate' => $candidate,
            'sources' => $sources,
            'proposals' => $proposals,
            'records' => $records
        ]);
        Response::html($html);
    }

    public function checklist(Request $request): void
    {
        $pdo = Connection::get();
        $offices = $pdo->query("SELECT id, code, name FROM offices ORDER BY id ASC")->fetchAll();

        $html = View::render('checklist', [
            'offices' => $offices
        ]);
        Response::html($html);
    }

    public function sources(Request $request): void
    {
        $pdo = Connection::get();
        $sources = $pdo->query("SELECT * FROM sources WHERE active = 1 ORDER BY id ASC")->fetchAll();

        $html = View::render('sources', [
            'sources' => $sources
        ]);
        Response::html($html);
    }

    public function methodology(Request $request): void
    {
        $html = View::render('methodology');
        Response::html($html);
    }

    public function about(Request $request): void
    {
        $html = View::render('about');
        Response::html($html);
    }

    public function privacy(Request $request): void
    {
        $html = View::render('privacy');
        Response::html($html);
    }

    public function status(Request $request): void
    {
        $pdo = Connection::get();
        $imports = $pdo->query("SELECT * FROM data_imports ORDER BY id DESC LIMIT 20")->fetchAll();
        $stats = [
            'total_candidates' => (int)$pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn(),
            'total_parties' => (int)$pdo->query("SELECT COUNT(*) FROM parties")->fetchColumn(),
            'last_sync' => $pdo->query("SELECT MAX(finished_at) FROM data_imports WHERE status IN ('completed', 'partial')")->fetchColumn()
        ];

        $html = View::render('status', [
            'imports' => $imports,
            'stats' => $stats
        ]);
        Response::html($html);
    }
}

