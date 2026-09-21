/**
 * Gerenciador da Cola Eleitoral Local (LGPD Compliant)
 * Armazena as preferências e notas do eleitor unicamente no localStorage do navegador.
 * Suporta 2 senadores por estado (regra eleitoral brasileira).
 */

const ColaEleitoral = {
    STORAGE_KEY: 'eleitor_consciente_cola_2026',

    get() {
        try {
            const raw = localStorage.getItem(this.STORAGE_KEY);
            return raw ? JSON.parse(raw) : {};
        } catch (e) {
            console.error('Erro ao ler cola eleitoral do localStorage:', e);
            return {};
        }
    },

    save(data) {
        try {
            localStorage.setItem(this.STORAGE_KEY, JSON.stringify(data));
            window.dispatchEvent(new CustomEvent('cola:updated', { detail: data }));
            return true;
        } catch (e) {
            console.error('Erro ao salvar cola eleitoral no localStorage:', e);
            return false;
        }
    },

    /**
     * Adiciona candidato à cola eleitoral.
     * Para SENADOR, auto-gerencia slots SENADOR_1 e SENADOR_2.
     */
    addCandidate(candidate, slotCode) {
        const cola = this.get();
        const rawCode = String(candidate.office_code).toUpperCase();

        // Lógica especial para senadores (2 vagas)
        let code = rawCode;
        if (rawCode === 'SENADOR') {
            code = slotCode || this._findSenatorSlot(cola);
        }

        cola[code] = {
            id: Number(candidate.id),
            office_code: code,
            base_office: rawCode === 'SENADOR' ? 'SENADOR' : rawCode,
            office_name: candidate.office_name,
            ballot_number: candidate.ballot_number,
            ballot_name: candidate.ballot_name,
            party_acronym: candidate.party_acronym,
            photo_url: candidate.photo_url || null,
            note: (cola[code] && cola[code].note) ? cola[code].note : '',
            saved_at: new Date().toISOString()
        };
        return this.save(cola);
    },

    /**
     * Encontra o próximo slot de senador disponível.
     */
    _findSenatorSlot(cola) {
        if (!cola['SENADOR_1']) return 'SENADOR_1';
        if (!cola['SENADOR_2']) return 'SENADOR_2';
        return 'SENADOR_1'; // Substitui o primeiro se ambos estiverem cheios
    },

    /**
     * Retorna o número de senadores adicionados.
     */
    senatorCount() {
        const cola = this.get();
        let count = 0;
        if (cola['SENADOR_1']) count++;
        if (cola['SENADOR_2']) count++;
        return count;
    },

    /**
     * Verifica se um candidato já está na cola (em qualquer slot).
     */
    hasCandidate(candidateId) {
        const cola = this.get();
        const cid = Number(candidateId);
        return Object.values(cola).some(item => item && item.id === cid);
    },

    /**
     * Retorna o slot code de um candidato (SENADOR_1, SENADOR_2, etc).
     */
    findCandidateSlot(candidateId) {
        const cola = this.get();
        const cid = Number(candidateId);
        for (const [code, item] of Object.entries(cola)) {
            if (item && item.id === cid) return code;
        }
        return null;
    },

    removeCandidate(officeCode) {
        const cola = this.get();
        const code = String(officeCode).toUpperCase();
        if (cola[code]) {
            delete cola[code];
            return this.save(cola);
        }
        return false;
    },

    updateNote(officeCode, note) {
        const cola = this.get();
        const code = String(officeCode).toUpperCase();
        if (cola[code]) {
            cola[code].note = note;
            return this.save(cola);
        }
        return false;
    },

    count() {
        const cola = this.get();
        return Object.keys(cola).length;
    },

    clear() {
        localStorage.removeItem(this.STORAGE_KEY);
        window.dispatchEvent(new CustomEvent('cola:updated', { detail: {} }));
        return true;
    }
};

window.ColaEleitoral = ColaEleitoral;
