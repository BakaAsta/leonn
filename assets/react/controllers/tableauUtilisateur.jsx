import React, { useState, useEffect } from 'react';
import { FilterMatchMode, FilterOperator } from 'primereact/api';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { InputText } from 'primereact/inputtext';
import { IconField } from 'primereact/iconfield';
import 'primereact/resources/themes/saga-blue/theme.css';  
import 'primereact/resources/primereact.min.css';
import 'primeicons/primeicons.css';
import { Button } from 'primereact/button';
import { locale, addLocale } from 'primereact/api';

export default function TableauUser(props) {
    const [utilisateurs, setUtilisateurs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [globalFilterValue, setGlobalFilterValue] = useState('');
    const [filters, setFilters] = useState(null);

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
        fetchDataUser();
        initFilters();
    }, []);

    const fetchDataUser = () => {
        fetch(apiUrl + '/users', {
            headers: {
                Authorization: 'Bearer ' + props.token,
            },
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const userData = data['hydra:member'].map(user => (
                {
                    ...user,
                    email: user.email,
                    
                }));
                setUtilisateurs(userData);
                console.log(userData);
                setLoading(false);
            });
    };

    const initFilters = () => {
        setFilters({
            'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
            'email': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
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

    const clearFilters = () => {
        initFilters();
    };

    const renderHeader = () => {
        return (
            <div className="flex justify-between relative">
                <Button type="button" icon="pi pi-filter-slash" outlined onClick={clearFilters} />
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

    const header = renderHeader();

    const SuppButton = ({ id }) => {
        const [isDeleting, setIsDeleting] = useState(false);
    
        const deleteUser = (id) => {
            setIsDeleting(true);
            fetch(`/api/users/${id}`, {
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
                fetchDataUser();
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
                onClick={() => deleteUser(id)}
                disabled={isDeleting}
                className={`text-slate-800 hover:text-white text-sm bg-white hover:bg-red-400 border border-slate-200 rounded-lg font-medium px-4 py-2 inline-flex space-x-1 items-center ${isDeleting ? 'opacity-50 pointer-events-none' : ''}`}
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className="w-6 h-6 hover:stroke-white">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
        );
    };

    return (
        <div className="card">
            <DataTable className="w-full text-sm"
                value={utilisateurs}
                removableSort
                paginator
                rows={10}
                dataKey="id"
                filters={filters}
                loading={loading}
                globalFilterFields={['email']}
                header={header}
                emptyMessage="Aucun utilisateur trouvé"
            >
                <Column field='email' header="Email" filter filterPlaceholder='Recherche par utilisateur' sortable className="w-1/11" />
                <Column filed='roles' header="Rôle Utilisateur" filter className="w-1/11" body={(rowData) => {
                    return (
                        <div>
                            {rowData.roles.map((role, index) => (
                                <span
                                    key={index}
                                    className={`text-sm rounded px-1 ltr:ml-2 rtl:ml-2 mb-1 mt-1 font-bold ${
                                        role === 'ROLE_ADMIN' ? 'bg-info-light text-info' : 'bg-success-light text-success'
                                    }`}
                                >
                                    {role}
                                </span>
                            ))}
                        </div>
                    );
                } }/>
                <Column
                    header="Actions"
                    className="w-1/11"
                    body={(rowData) => (
                        <div className="flex justify-start items-center">
                            <SuppButton id={rowData.id} onDelete={true} />
                        </div>
                    )}
                />

            </DataTable>
        </div>
    );
}
