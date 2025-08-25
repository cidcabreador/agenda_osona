// public/js/organitzadors.js (Versió corregida i robusta)

document.addEventListener('DOMContentLoaded', function () {
    const orgForm = document.getElementById('filtres-organitzadors-form');
    // ===== CANVI CLAU: Usem el nou ID del contenidor =====
    const resultsContainer = document.getElementById('organitzadors-results-container');

    if (!orgForm || !resultsContainer) return;

    let currentSort = { by: 'nom', order: 'ASC' };
    let currentPage = 1;
    let debounceTimer;

    const updateOrgSortIcons = () => {
        resultsContainer.querySelectorAll('.sort-link i').forEach(icon => {
            icon.className = 'fa-solid fa-sort text-muted';
        });
        const activeSortLink = resultsContainer.querySelector(`.sort-link[data-sort-by="${currentSort.by}"] i`);
        if (activeSortLink) {
            activeSortLink.className = `fa-solid fa-sort-${currentSort.order.toLowerCase()}`;
        }
    };

    const fetchOrganitzadors = () => {
        const formData = new FormData(orgForm);
        const params = new URLSearchParams(formData);
        params.append('sort_by', currentSort.by);
        params.append('sort_order', currentSort.order);
        params.append('pagina', currentPage);
        
        const loadingIndicator = document.getElementById('loading-indicator-org');
        const tableBody = document.getElementById('taula-organitzadors-body');
        
        if(loadingIndicator) loadingIndicator.classList.remove('d-none');
        if(tableBody) tableBody.style.opacity = '0.5';

        fetch(`index.php?accio=filtrar_organitzadors_ajax&${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) { alert(data.error); return; }
                if(tableBody) tableBody.innerHTML = data.taula_html;
                document.getElementById('paginacio-organitzadors-container').innerHTML = data.paginacio_html;
                updateOrgSortIcons();
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                if(loadingIndicator) loadingIndicator.classList.add('d-none');
                if(tableBody) tableBody.style.opacity = '1';
            });
    };

    // Listener per als filtres
    orgForm.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentPage = 1;
            fetchOrganitzadors();
        }, 350);
    });
    
    // Listener central per a la taula i la paginació
    resultsContainer.addEventListener('click', e => {
        const sortLink = e.target.closest('.sort-link');
        const pageLink = e.target.closest('.page-link');

        if (sortLink) {
            e.preventDefault();
            const newSortBy = sortLink.dataset.sortBy;
            if (currentSort.by === newSortBy) {
                currentSort.order = currentSort.order === 'ASC' ? 'DESC' : 'ASC';
            } else {
                currentSort.by = newSortBy;
                currentSort.order = 'ASC';
            }
            currentPage = 1;
            fetchOrganitzadors();
        }

        if (pageLink) {
            e.preventDefault();
            const newPage = pageLink.dataset.pagina;
            if (newPage && newPage != currentPage) {
                currentPage = newPage;
                fetchOrganitzadors();
            }
        }
    });

    updateOrgSortIcons();
});