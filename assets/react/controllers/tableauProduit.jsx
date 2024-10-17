import React, { useState, useEffect } from 'react';
import { FilterMatchMode, FilterOperator } from 'primereact/api';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { InputText } from 'primereact/inputtext';
import { IconField } from 'primereact/iconfield';
import 'primereact/resources/themes/saga-blue/theme.css';  
import 'primereact/resources/primereact.min.css';
import 'primeicons/primeicons.css';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faTimes, faCheck, faBook } from '@fortawesome/free-solid-svg-icons';
import { Button } from 'primereact/button';
import { Sidebar } from 'primereact/sidebar';
import { Dropdown } from 'primereact/dropdown';
import { locale, addLocale } from 'primereact/api';

export default function AdvancedFilterDemo(props) {
    const [products, setProducts] = useState([]);
    const [filters, setFilters] = useState(null);
    const [loading, setLoading] = useState(true);
    const [idLastProduit, setIdLastProduit] = useState();
    const [globalFilterValue, setGlobalFilterValue] = useState('');
    const [visibleRight, setVisibleRight] = useState(false);
    const [selectedPret, setSelectedPret] = useState(null);
    const [lienEditionSelectedPret, setLienEditionSelectedPret] = useState(null);

    const apiUrl = 'https://' + window.location.host + '/api';
  
    useEffect(() => {
        addLocale('fr', {
            startsWith: 'Commence par',
            contains: 'Contient',
            notContains: 'Ne contient pas',
            endsWith: 'Finit par',
            equals: 'Égal à',
            notEquals: 'Différent de',
            noFilter: 'Aucun filtre',
            lt: 'Moins que',
            lte: 'Inférieur ou égal à',
            gt: 'Plus grand que',
            gte: 'Supérieur ou égal à',
            dateIs: 'Date égale à',
            dateIsNot: 'Date différente de',
            dateBefore: 'Date avant',
            dateAfter: 'Date après',
            clear: 'Effacer',
            apply: 'Appliquer',
            matchAll: 'Correspond à tous',
            matchAny: 'Correspond à n\'importe lequel',
            addRule: 'Ajouter une règle',
            removeRule: 'Supprimer la règle',
            accept: 'Oui',
            reject: 'Non',
            choose: 'Choisir',
            upload: 'Uploader',
            cancel: 'Annuler',
            dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
            dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
            monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthNamesShort: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            today: 'Aujourd\'hui',
            weekHeader: 'Sm',
            firstDayOfWeek: 1,
            dateFormat: 'dd/mm/yy',
            weak: 'Faible',
            medium: 'Moyen',
            strong: 'Fort',
            passwordPrompt: 'Entrer un mot de passe',
            emptyMessage: 'Aucune option disponible',
            emptyFilterMessage: 'Aucun résultat trouvé'
            });
            locale('fr');
        fetchData();
        initFilters();
    }, []);

    const fetchData = () => {
        
        fetch( apiUrl + '/produits', {
            headers: {
                Authorization: 'Bearer ' + props.token,
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const productsData = data['hydra:member'].map(product => ({
                ...product,
                categories: product.categories.map(category => category.nom).join(', '),
                marque: product.marque ? product.marque.nom : 'Non défini',
                typeProduit: product.typeProduit.map(tp => tp.nom).join(', '),
                dateRebus: product.dateRebus ? new Date(product.dateRebus).toLocaleDateString('fr').split('/').reverse().join('/') : '',
                updatedAt: new Date(product.updatedAt).toLocaleDateString().split('/').reverse().join('/')
            }));
            setProducts(productsData);
            console.log(productsData);
            setIdLastProduit(productsData[productsData.length - 1].id);
            setLoading(false);
        });
    };

    const initFilters = () => {
        setFilters({
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
            nom: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            refInterne: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            refFabricant: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            categories: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            commentaire: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }] },
            marque: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            etat: { label: 'Disponible', value: 'Disponible', matchMode: FilterMatchMode.EQUALS },
        });
        setGlobalFilterValue('');
    };

    const onGlobalFilterChange = (e) => {
        const value = e.target.value;
        let _filters = { ...filters };
        _filters['global'].value = value;
        setFilters(_filters);
        setGlobalFilterValue(value);
    };

    const clearFilter = () => {
        initFilters();
    };

    const renderHeader = () => {
        return (
            <div className="flex justify-between relative">
                <Button type="button" icon="pi pi-filter-slash" outlined onClick={clearFilter} />
                <IconField iconPosition="left">
                    <button
                        type="button"
                        className="absolute w-9 h-9 inset-0 ltr:right-auto rtl:left-auto appearance-none peer-focus:text-primary"
                    >
                        <svg className="mx-auto" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" strokeWidth="1.5" opacity="0.5" />
                            <path d="M18.5 18.5L22 22" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" />
                        </svg>
                    </button>
                    <InputText
                        value={globalFilterValue}
                        onChange={onGlobalFilterChange}
                        placeholder="Mot-clés de recherche"
                        className="form-input ltr:pl-9 rtl:pr-9 ltr:sm:pr-4 rtl:sm:pl-4 ltr:pr-9 rtl:pl-9 peer sm:bg-transparent bg-gray-100 placeholder:tracking-widest"
                    />
                </IconField>
            </div>
        );
    };

    const countryBodyTemplate = (rowData) => {
        return (
            <div className="flex align-items-center gap-2">
                <span>{rowData.nom}</span>
            </div>
        );
    };

    const balanceBodyTemplate = (rowData) => {
        return rowData.quantite;
    };

    

    const constructPath = (url, params) => {
    return `${url}${params}`;
    }; 

    const ModifButton = ({ url, id, color }) => {
        const path = constructPath(url,  id );
        return (
            <a href={path} className="inline-flex items-center space-x-1">
            <button className="text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 rounded-l-lg font-medium px-4 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className={`w-6 h-6 ${color}`}>
                <path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path>
                </svg>
            </button>
            </a>
        );
    };
    const duplicateButton = ({ id }) => {
        const [isDuplicating, setIsDuplicating] = useState(false);
    
        const fetchProduct = async (id) => {
            try {
                const response = await fetch(`/api/produits/${id}`, {
                    method: 'GET',
                    headers: {
                        Authorization: 'Bearer ' + props.token,
                    },
                });
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                // fetchData();
                return response.json();
            } catch (error) {
                console.error('Error fetching product:', error);
                throw error; // Propagate the error to be handled later
            }
        };
    
        const createProduct = async (p) => {
            try {
                const response = await fetch(`/api/produits`, {
                    method: 'POST',
                    headers: {
                        Authorization: 'Bearer ' + props.token,
                        'Content-Type': 'application/ld+json', // Ensure the request is sent as JSON
                    },
                    body: p,
                });
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                const newProduct = await response.json();
                fetchData();
                return newProduct;
            } catch (error) {
                console.error('Error creating product:', error);
                throw error;
            }
        };

        const updateProduit = async (p, id) => {
            try {
                const response = await fetch(`/api/produits/${id}`, {
                    method: 'PUT',
                    headers: {
                        Authorization: 'Bearer ' + props.token,
                        'Content-Type': 'application/ld+json', // Ensure the request is sent as JSON
                    },
                    body: p,
                });
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                const updatedProduct = await response.json();
                fetchData();
                return updatedProduct;
        } 
        catch (error) {
            console.error('Error duplicating product:', error);
        }
    };
    
    
        const duplicateProduct = async (id) => {
            setIsDuplicating(true);
            try {
                const product = await fetchProduct(id);

                // changement de la logique. Si le produit existent déjà et qu'il est consommable alors on incrémente la quantité
                // Sinon on crée un nouveau produit

                if ((JSON.stringify(product.typeProduit)).includes('Immo')) {
                    product.quantite += 1;
                    product.etat = 'Disponible';
                    console.log(product);

                    const finalProduct = {
                        "nom": product.nom,
                        "refInterne": product.refInterne,
                        "refFabricant": product.refFabricant,
                        "commentaire": product.commentaire || '',
                        "etat": product.etat,
                        "dateRebus": product.dateRebus,
                        "createAt": product.createAt,
                        "updatedAt": product.updatedAt,
                        "typeProduit": product.typeProduit.map(tp => tp['@id']),
                        "categories": product.categories.map(category => category['@id']),
                        "marque": product.marque ? product.marque['@id'] : null, 
                        "quantite": product.quantite,
                    };
                    updateProduit(JSON.stringify(finalProduct), id);
                }

                else {
    
                const date = new Date();
                const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
                const formattedDate = date.toLocaleDateString('fr-FR', options);
                
                // Reformater en ISO 8601
                const [day, month, year] = formattedDate.split('/');
                const isoDate = `${day}-${month}-${year}`;
                
                console.log(isoDate); // Output: 2024-07-15
                const suppSlashDate = formattedDate.replace(/\//g, '');
                
                console.log(formattedDate); // Output: 15/07/2024
                console.log(suppSlashDate); // Output: 15072024
                const newRefInterne = suppSlashDate + '00' + (idLastProduit + 1); // Ensure idLastProduit is defined and accessible
                product.etat = 'Disponible';
                product.dateRebus = null;
    
                const finalProduct = {
                    "nom": product.nom,
                    "refInterne": newRefInterne,
                    "refFabricant": product.refFabricant,
                    "commentaire": product.commentaire || '',
                    "etat": product.etat,
                    "dateRebus": product.dateRebus,
                    "createAt": isoDate,
                    "updatedAt": isoDate,
                    "typeProduit": product.typeProduit.map(tp => tp['@id']),
                    "categories": product.categories.map(category => category['@id']),
                    "marque": product.marque ? product.marque['@id'] : null,
                    "quantite": product.quantite,
                };
    
                await createProduct(JSON.stringify(finalProduct));
                // Mettre à jour l'état des produits avec le nouveau produit
                
                }
            }
             catch (error) {
                setIsDuplicating(false);
                console.error('Error duplicating product:', error);
            } finally {
                setIsDuplicating(false);
            }
        };
    
        return (
            <button
                onClick={() => duplicateProduct(id)}
                disabled={isDuplicating}
                className={`text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 font-medium px-4 py-2`}
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="1.7em" height="1.7em" viewBox="0 0 512 512">
                    <rect width="336" height="336" x="128" y="128" fill="none" stroke="currentColor" strokeLinejoin="round" strokeWidth="32" rx="57" ry="57" />
                    <path fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="32" d="m383.5 128l.5-24a56.16 56.16 0 0 0-56-56H112a64.19 64.19 0 0 0-64 64v216a56.16 56.16 0 0 0 56 56h24m168-168v160m80-80H216" />
                </svg>
            </button>
        );
    };
    
            
        
    const SuppButton = ({ id }) => {
        const [isDeleting, setIsDeleting] = useState(false);
    
        const deleteProduct = (id) => {
            setIsDeleting(true);
            fetch(`/api/produits/${id}`, {
                method: 'DELETE',
                headers: {
                    Authorization: 'Bearer ' + props.token,
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                // Si la suppression réussit, mettre à jour localement l'état des produits en filtrant le produit supprimé
                setProducts(products.filter(product => product.id !== id));
            })
            .catch(error => {
                console.error('Error deleting product:', error);
            })
            .finally(() => {
                setIsDeleting(false);
            });
        };
    
        return (
            <button
                onClick={() => deleteProduct(id)}
                disabled={isDeleting}
                className={`text-slate-800 hover:text-white text-sm bg-white hover:bg-red-400 border border-slate-200 rounded-r-lg font-medium px-4 py-2 inline-flex space-x-1 items-center ${isDeleting ? 'opacity-50 pointer-events-none' : ''}`}
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className="w-6 h-6 hover:stroke-white">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
        );
    };

    const header = renderHeader();

    const produitPretEnCours = (rowData) => {
        for (let i = 0; i < rowData.prets.length; i++) {
            if (!rowData.prets[i].dateFin || new Date(rowData.prets[i].dateFin) > new Date()){
                let lienEdition = `/pret/editer/${rowData.prets[i].id}`;
                setLienEditionSelectedPret(lienEdition);
                console.log(rowData.prets[i]);
                setSelectedPret(rowData.prets[i]);
                return rowData.prets[i];
            }
        }
        return null;
    }

    const passageRebusProduit = async (p, id) => {
        try {

            const fetchProduct = async (id) => {
                try {
                    const response = await fetch(`/api/produits/${id}`, {
                        method: 'GET',
                        headers: {
                            Authorization: 'Bearer ' + props.token,
                        },
                    });
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    // fetchData();
                    return response.json();
                } catch (error) {
                    console.error('Error fetching product:', error);
                    throw error; // Propagate the error to be handled later
                }
            };

            const updateProduit = async (p, id) => {
                try {
                    const response = await fetch(`/api/produits/${id}`, {
                        method: 'PUT',
                        headers: {
                            Authorization: 'Bearer ' + props.token,
                            'Content-Type': 'application/ld+json', // Ensure the request is sent as JSON
                        },
                        body: p,
                    });
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    const updatedProduct = await response.json();
                    fetchData();
                    return updatedProduct;
            } 
            catch (error) {
                console.error('Error duplicating product:', error);
            }
        };

            const product = await fetchProduct(id);
            product.etat = 'rebus';
            product.dateRebus = new Date().toISOString();

            // changement de la logique. Si le produit existent déjà et qu'il est consommable alors on incrémente la quantité
            // Sinon on crée un nouveau produit

                const finalProduct = {
                    "nom": product.nom,
                    "refInterne": product.refInterne,
                    "refFabricant": product.refFabricant,
                    "commentaire": product.commentaire || '',
                    "etat": product.etat,
                    "dateRebus": product.dateRebus,
                    "createAt": product.createAt,
                    "updatedAt": product.updatedAt,
                    "typeProduit": product.typeProduit.map(tp => tp['@id']),
                    "categories": product.categories.map(category => category['@id']),
                    "marque": product.marque ? product.marque['@id'] : null, 
                    "quantite": product.quantite,
                };
                updateProduit(JSON.stringify(finalProduct), id);
            }
        catch (error) {
            console.error('Error duplicating product:', error);
        }
    };

    const passageEnStockProduit = async (p, id) => {
        try {

            const fetchProduct = async (id) => {
                try {
                    const response = await fetch(`/api/produits/${id}`, {
                        method: 'GET',
                        headers: {
                            Authorization: 'Bearer ' + props.token,
                        },
                    });
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    // fetchData();
                    return response.json();
                } catch (error) {
                    console.error('Error fetching product:', error);
                    throw error; // Propagate the error to be handled later
                }
            };

            const updateProduit = async (p, id) => {
                try {
                    const response = await fetch(`/api/produits/${id}`, {
                        method: 'PUT',
                        headers: {
                            Authorization: 'Bearer ' + props.token,
                            'Content-Type': 'application/ld+json', // Ensure the request is sent as JSON
                        },
                        body: p,
                    });
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    const updatedProduct = await response.json();
                    fetchData();
                    return updatedProduct;
            } 
            catch (error) {
                console.error('Error duplicating product:', error);
            }
        };

            const product = await fetchProduct(id);
            product.etat = 'Disponible';
            product.dateRebus = null;

            // changement de la logique. Si le produit existent déjà et qu'il est consommable alors on incrémente la quantité
            // Sinon on crée un nouveau produit

                const finalProduct = {
                    "nom": product.nom,
                    "refInterne": product.refInterne,
                    "refFabricant": product.refFabricant,
                    "commentaire": product.commentaire || '',
                    "etat": product.etat,
                    "dateRebus": product.dateRebus,
                    "createAt": product.createAt,
                    "updatedAt": product.updatedAt,
                    "typeProduit": product.typeProduit.map(tp => tp['@id']),
                    "categories": product.categories.map(category => category['@id']),
                    "marque": product.marque ? product.marque['@id'] : null, 
                    "quantite": product.quantite,
                };
                updateProduit(JSON.stringify(finalProduct), id);
            }
        catch (error) {
            console.error('Error duplicating product:', error);
        }
    };

    const stockRowFilterTemplate = (options) => {
        return (
            <Dropdown
                value={options.value}
                options={[
                    { label: 'Rebus', value: 'rebus' },
                    { label: 'Disponible', value: 'Disponible' },
                    { label: 'Pret', value: 'pret'}
                ]}
                onChange={(e) => options.filterApplyCallback(e.value)}
                placeholder="Sélectionner"
                className="p-column-filter"
                showClear
                style={{ minWidth: '12rem' }}
            />
        );
    };

    const stockBodyTemplate = (rowData) => {
        return (
            <div className="flex items-center">
                {rowData.etat == 'rebus' ? (
                <button onClick={() => passageEnStockProduit(rowData, rowData.id)}>
                    <FontAwesomeIcon icon={faTimes} className="text-red-500" />
                </button>
                ) : (
                    rowData.etat == 'pret' && rowData.prets.length > 0 && (
                        rowData.prets.some(pret => !pret.dateFin || new Date(pret.dateFin) > new Date())
                    )
                     ? (
                        <button icon="pi pi-arrow-right" onClick={() => { setVisibleRight(true), produitPretEnCours(rowData) }}>
                        <div className="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.4em" height="1.4em" viewBox="0 0 32 32">
                            <path fill="#F97316" d="m21 3.031l-.656.719c-1.469 1.617-2.68 2.34-3.688 2.813c-1.008.472-1.855.613-2.687 1.25c-.887.68-2.176 1.984-2.719 4.312c-1.164.254-2.016.7-2.688 1.281c-.664.574-1.164 1.227-1.78 1.938c-.005.008.003.023 0 .031c-.884 1.016-1.657 2.11-3.157 2.688l-.625.25V29h19.063c1.093-.059 1.851-.816 2.312-1.563c.46-.746.715-1.554.844-2.218c.332-1.692.937-6.563.937-6.563l.032-.093v-.094c-.032-.676-.31-1.25-.657-1.782l1.125-3.343l1.782-2.688l.5-.719l-.657-.593l-6.562-5.688zm.063 2.75l5.218 4.532l-1.375 2.03l-.093.095l-.032.156l-.906 2.687c-.473-.195-.96-.332-1.5-.312h-.063L16 15h-1v3.875c-.14 1.09-.746 1.512-1.5 1.813c-.25.101-.281.046-.5.093V14.97c-.164-3.707 1.156-4.774 2.188-5.563c.285-.219 1.12-.472 2.312-1.031c.996-.469 2.234-1.309 3.563-2.594zm-10 8.594c-.004.227-.075.387-.063.625v8h1s1.07-.012 2.219-.469c1.148-.457 2.535-1.527 2.781-3.406V17l5.375-.031h.031a1.662 1.662 0 0 1 1.75 1.562c-.004.016-.05.387-.062.469H20v2h3.844c-.106.773-.203 1.258-.313 2H20v2h3.219a5.002 5.002 0 0 1-.563 1.375c-.273.445-.508.613-.718.625H5v-7.469c1.621-.86 2.629-2.097 3.281-2.843c.676-.774 1.14-1.36 1.594-1.75c.297-.254.762-.399 1.188-.563"/>
                        </svg>
                        </div>
                    </button>
                    ) : (
                        rowData.etat == 'Disponible' && (
                    <button onClick={() => passageRebusProduit(rowData, rowData.id)}>
                        <FontAwesomeIcon icon={faCheck} className="text-green-500" />
                    </button>
                    )
                    )
                )}
            </div>
        );
    };

    const buttonEditionPret = ({ lienEditionSelectedPret }) => {
        return (
          <a href={lienEditionSelectedPret} className="btn btn-outline-primary">
            Editer
          </a>
        );
      };
    

    return (
        <div className="card">
            <DataTable className="w-full text-sm"
                value={products}
                removableSort
                paginator
                rows={10}
                dataKey="id"
                filters={filters}
                loading={loading}
                globalFilterFields={['nom', 'refInterne', 'refFabricant', 'commentaire', 'marque']}
                header={header}
                emptyMessage="Aucun produit trouvé"
            >
                <Column field="nom" header="Nom" filter  filterPlaceholder="Rechercher par nom" sortable className="w-1/11 custom-filter" body={countryBodyTemplate} />
                <Column field="refInterne" header="Référence Interne" filter filterPlaceholder="Rechercher par refInterne" sortable className="w-1/11" />
                <Column field="refFabricant" header="Ref Fabricant" filter filterPlaceholder="Rechercher par refFabricant" sortable className="w-1/11" />
                <Column field="marque" header="Marque" filter filterPlaceholder="Rechercher par marque" sortable className="w-1/11" />
                <Column field="quantite" header="Quantité" sortable className="w-1/11" body={balanceBodyTemplate} />
                <Column
                    field="typeProduit"
                    header="Type de Produit"
                    sortable
                    className="w-1/11"
                    body={(rowData) => (
                        <div className="flex space-x-2">
                            {rowData.typeProduit.includes('Immo') && (
                                <span className="btn rounded-lg text-[#66E2AC] border-[#66E2AC] hover:bg-[#66E2AC] hover:text-white py-1 px-3 inline-block text-center">
                                    Immo
                                </span>
                            )}
                            {rowData.typeProduit.includes('Pret') && (
                                <span className="btn rounded-lg text-[#87CEEB] border-[#87CEEB] hover:bg-[#87CEEB] hover:text-white py-1 px-3 inline-block text-center">
                                    Pret
                                </span>
                            )}
                            {rowData.typeProduit.includes('Affectation') && (
                                <span className="btn rounded-lg text-[#FFA500] border-[#FFA500] hover:bg-[#FFA500] hover:text-white py-1 px-3 inline-block text-center">
                                    Affectation
                                </span>
                            )}
                        </div>
                    )}
                />
                <Column
                    field="etat"
                    header="En Stock"
                    body={stockBodyTemplate}
                    filter
                    filterElement={stockRowFilterTemplate}
                />
                <Column field="dateRebus" header="Date de Rebus" className="w-1/11" body={(rowData) =>(
                    rowData.dateRebus ? new Date(rowData.dateRebus).toLocaleDateString() : ''
                ) 

                }/>
                <Column field="categories" header="Categories" filter filterPlaceholder="Rechercher par categories" sortable className="w-1/11" />
                <Column field="commentaire" header="Commentaire" filter filterPlaceholder="Rechercher par commentaire" className="w-1/11" />
                {/* <Column field="updatedAt" header="Dernière Mise à jour" className="w-1/11" /> */}
                <Column header="Actions" className="w-1/11" body={(rowData) => (
                    <div className="flex justify-start items-center">
                        {ModifButton({ url: 'editer/', id: rowData.id })}
                        {/* bouton de duplication */}
                        {duplicateButton({ id: rowData.id })}
                        {rowData.prets.length === 0 ? <SuppButton id={rowData.id} onDelete={true} /> : null}
                    </div>
                )} />
            </DataTable>
            {/* <Button icon="pi pi-arrow-right" onClick={() => setVisibleLeft(true)} /> */}
            <Sidebar 
            visible={visibleRight} 
            position="right" 
            onHide={() => setVisibleRight(false)} 
            className="w-[30%]" 
            style={{ width: '30%' }}
        >
            {selectedPret ? (
                <div className="panel container relative flex flex-col justify-between max-w-6xl px-10 mx-auto xl:px-0 mt-5">
                    <h2 className="mb-3 text-3xl font-extrabold leading-tight text-gray-900 text-center">Récapitulatif</h2>
                    <div className="flex flex-wrap mb-10 -mx-2">
                        <div className="w-full px-2 mb-4 sm:w-1/2">
                            <div className="relative h-full ml-0 mr-0 sm:mr-10">
                                <span className="absolute top-0 left-0 w-full h-full mt-1 ml-1 bg-indigo-500 rounded-lg"></span>
                                <div className="relative h-full p-5 bg-white border-2 border-indigo-500 rounded-lg flex flex-col justify-center text-center overflow-hidden">
                                    <div className="flex items-center justify-center -mt-1">
                                        <h3 className="my-2 ml-3 text-lg font-bold text-gray-800">Utilisateur Concerné</h3>
                                    </div>
                                    <p className="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl  text-ellipsis">
                                        {selectedPret.user.email ? selectedPret.user.email : 'Aucune utilisateur'}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="w-full px-2 mb-4 sm:w-1/2">
                            <div className="relative h-full ml-0 mr-0 sm:mr-10">
                                <span className="absolute top-0 left-0 w-full h-full mt-1 ml-1 bg-blue-400 rounded-lg"></span>
                                <div className="relative h-full p-5 bg-white border-2 border-blue-400 rounded-lg flex flex-col justify-center text-center overflow-hidden">
                                    <div className="flex items-center justify-center -mt-1">
                                        <h3 className="my-2 ml-3 text-lg font-bold text-gray-800">Date de fin Prévue:</h3>
                                    </div>
                                    <p className="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl text-ellipsis">
                                        {selectedPret.dateFinPrevue ? new Date(selectedPret.dateFinPrevue).toLocaleDateString() : 'Aucune date renseignée'}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="w-full px-2 mb-4 sm:w-1/2">
                            {/* bouton Editer */}
                            <div className="absolute bottom-4 left-6">
                                {buttonEditionPret({ lienEditionSelectedPret })}
                            </div>
                        </div>
                    </div>
                </div>
            ) : (
                <p>Aucune information de prêt disponible.</p>
            )}
        </Sidebar>
        </div>
    );
}
