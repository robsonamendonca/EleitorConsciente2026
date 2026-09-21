/**
 * Cliente REST API para o Eleitor Consciente 2026
 */

const Api = {
    baseUrl: '/api/v1',

    async get(endpoint, params = {}) {
        const url = new URL(this.baseUrl + endpoint, window.location.origin);
        Object.keys(params).forEach(k => {
            if (params[k] !== undefined && params[k] !== null && params[k] !== '') {
                url.searchParams.append(k, params[k]);
            }
        });

        const res = await fetch(url.toString(), {
            headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) {
            const errData = await res.json().catch(() => ({}));
            throw new Error(errData.error?.message || `Erro HTTP ${res.status}`);
        }

        return await res.json();
    },

    getCandidates(params) {
        return this.get('/candidates', params);
    },

    getCandidate(id) {
        return this.get(`/candidates/${id}`);
    },

    getOffices() {
        return this.get('/offices');
    },

    getParties() {
        return this.get('/parties');
    },

    getHealth() {
        return this.get('/health');
    }
};

window.Api = Api;

