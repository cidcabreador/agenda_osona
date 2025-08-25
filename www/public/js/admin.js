// public/js/admin.js (Versió Final Completa i Estructurada)

document.addEventListener('DOMContentLoaded', function () {

    // ===== LÒGICA DEL PANEL D'ESDEVENIMENTS AMB AJAX =====
    const adminForm = document.getElementById('filtres-esdeveniments-form-admin');
    const tableContainer = document.querySelector('#table-title')?.closest('.card');

    if (adminForm && tableContainer) {
        let currentSort = { by: 'data_inici', order: 'DESC' };
        let currentPage = 1;
        let debounceTimer;

        const updateSortIcons = () => {
            tableContainer.querySelectorAll('.sort-link i').forEach(icon => {
                icon.className = 'fa-solid fa-sort text-muted';
            });
            const activeSortLink = tableContainer.querySelector(`.sort-link[data-sort-by="${currentSort.by}"] i`);
            if (activeSortLink) {
                activeSortLink.className = `fa-solid fa-sort-${currentSort.order.toLowerCase()}`;
            }
        };

        const fetchAdminEvents = () => {
            const formData = new FormData(adminForm);
            const params = new URLSearchParams(formData);
            
            params.append('sort_by', currentSort.by);
            params.append('sort_order', currentSort.order);
            params.append('pagina', currentPage);
            
            const queryString = params.toString();
            let hasFilters = Array.from(formData.values()).some(val => val !== '');

            document.getElementById('loading-indicator')?.classList.remove('d-none');
            const filterIndicator = document.getElementById('filter-indicator');
            if(filterIndicator) {
                filterIndicator.classList.toggle('d-flex', hasFilters);
                filterIndicator.classList.toggle('d-none', !hasFilters);
            }
            document.getElementById('taula-esdeveniments-body').style.opacity = '0.5';

            fetch(`index.php?accio=filtrar_esdeveniments_ajax&${queryString}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) { alert(data.error); return; }
                    document.getElementById('taula-esdeveniments-body').innerHTML = data.taula_html;
                    document.getElementById('paginacio-container').innerHTML = data.paginacio_html;
                    updateSortIcons();
                })
                .catch(error => console.error('Error en la petició AJAX:', error))
                .finally(() => {
                    document.getElementById('loading-indicator')?.classList.add('d-none');
                    document.getElementById('taula-esdeveniments-body').style.opacity = '1';
                });
        };

        adminForm.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                currentPage = 1;
                fetchAdminEvents();
            }, 350);
        });
        
        tableContainer.addEventListener('click', e => {
            const sortLink = e.target.closest('.sort-link');
            const pageLink = e.target.closest('.page-link');

            if (sortLink) { e.preventDefault(); const newSortBy = sortLink.dataset.sortBy; if (currentSort.by === newSortBy) { currentSort.order = currentSort.order === 'ASC' ? 'DESC' : 'ASC'; } else { currentSort.by = newSortBy; currentSort.order = 'ASC'; } currentPage = 1; fetchAdminEvents(); }
            if (pageLink) { e.preventDefault(); const newPage = pageLink.dataset.pagina; if (newPage && newPage != currentPage) { currentPage = newPage; fetchAdminEvents(); } }
        });
        
        const handleReset = () => {
            adminForm.reset();
            currentPage = 1;
            currentSort = { by: 'data_inici', order: 'DESC' };
            document.getElementById('id_categoria_admin')?.dispatchEvent(new Event('change'));
            fetchAdminEvents();
        };
        document.getElementById('reset-filters-btn')?.addEventListener('click', handleReset);
        document.getElementById('clear-filters-btn')?.addEventListener('click', handleReset);
        
        updateSortIcons();
    }

    // ===== LÒGICA PER AL ROBOT SCRAPER AMB AJAX (MULTI-SCRAPER) =====
    const selectorScraper = document.getElementById('selector-scraper');
    const iniciarRobotBtn = document.getElementById('iniciar-robot-btn');
    
    if (selectorScraper && iniciarRobotBtn) {
        const resultatsTextarea = document.getElementById('resultats-robot');
        const spinner = iniciarRobotBtn.querySelector('.spinner-border');
        const btnText = iniciarRobotBtn.querySelector('.btn-text');
        const btnIcon = iniciarRobotBtn.querySelector('.fa-play');

        selectorScraper.addEventListener('change', function() {
            iniciarRobotBtn.disabled = this.value === "";
        });

        iniciarRobotBtn.addEventListener('click', () => {
            const scraperSeleccionat = selectorScraper.value;
            if (!scraperSeleccionat) {
                resultatsTextarea.value = "Si us plau, selecciona un scraper de la llista.";
                return;
            }

            iniciarRobotBtn.disabled = true;
            selectorScraper.disabled = true;
            spinner.classList.remove('d-none');
            btnIcon.classList.add('d-none');
            btnText.textContent = 'Executant...';
            resultatsTextarea.value = `Iniciant procés per a '${scraperSeleccionat}'...\nAixò pot trigar una estona.`;

            fetch(`index.php?accio=executar_robot_ajax&scraper=${scraperSeleccionat}`)
                .then(response => response.json())
                .then(data => {
                    resultatsTextarea.value = data.log;
                    const toastContainer = document.querySelector('.toast-container');
                    if (toastContainer && window.bootstrap) {
                        const toastHTML = `
                        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="10000">
                          <div class="toast-header bg-success text-white">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong class="me-auto">Procés Finalitzat</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                          </div>
                          <div class="toast-body">
                            El robot ha acabat. <a href="index.php?accio=controlar_robot" class="fw-bold">Recarrega la pàgina</a> per veure els nous esdeveniments per validar.
                          </div>
                        </div>`;
                        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
                        const newToast = new bootstrap.Toast(toastContainer.lastElementChild);
                        newToast.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultatsTextarea.value = 'S\'ha produït un error greu durant l\'execució del robot.';
                })
                .finally(() => {
                    iniciarRobotBtn.disabled = false;
                    selectorScraper.disabled = false;
                    spinner.classList.add('d-none');
                    btnIcon.classList.remove('d-none');
                    btnText.textContent = 'Iniciar el Robot';
                });
        });
    }

    // ===== LÒGICA PER AL FILTRE DEL LLISTAT MANUAL D'ORGANITZADORS =====
    const filtreInput = document.getElementById('filtre-organitzadors-manual');
    const organitzadorsSelect = document.getElementById('organitzadors_addicionals');

    if (filtreInput && organitzadorsSelect) {
        filtreInput.addEventListener('input', function() {
            const filtreText = this.value.toLowerCase().trim();
            const opcions = organitzadorsSelect.options;

            for (let i = 0; i < opcions.length; i++) {
                const textOpcio = opcions[i].text.toLowerCase();
                if (textOpcio.includes(filtreText)) {
                    opcions[i].style.display = ''; // Mostra l'opció
                } else {
                    opcions[i].style.display = 'none'; // Amaga l'opció
                }
            }
        });
    }
	
	
});

