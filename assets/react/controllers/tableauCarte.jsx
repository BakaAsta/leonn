import React, { useState, useEffect } from 'react';
import { FilterMatchMode, FilterOperator, locale, addLocale } from 'primereact/api';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { InputText } from 'primereact/inputtext';
import { IconField } from 'primereact/iconfield';
import 'primereact/resources/themes/saga-blue/theme.css';
import 'primereact/resources/primereact.min.css';
import 'primeicons/primeicons.css';
import { Button } from 'primereact/button';
import { Sidebar } from 'primereact/sidebar';
import { Form, Field } from 'react-final-form'; // Utilisez react-final-form à la place de react
import { Dialog } from 'primereact/dialog';
import { classNames } from 'primereact/utils';
import './FormDemo.css';

export default function TableauCarte(props) {
    const [cartes, setCartes] = useState([]);
    const [loading, setLoading] = useState(true);
    const [globalFilterValue, setGlobalFilterValue] = useState('');
    const [visibleRight, setVisibleRight] = useState(false);
    const [filters, setFilters] = useState({
        'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
        'reference': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        'valeur': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] }
    });
    const [idLastCard, setIdLastCard] = useState(null);

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
        fetchDataCarte();
    }, []);

    const fetchDataCarte = async () => {
        try {
            const response = await fetch(apiUrl + '/cartes', {
                headers: {
                    Authorization: 'Bearer ' + props.token,
                },
            });
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            const data = await response.json();
            const cartesData = data['hydra:member'].map(carte => ({
                id: carte.id,
                reference: carte.reference,
                valeur: carte.valeur,
                prets : carte.prets
            }));
            setCartes(cartesData);
            console.log(cartesData);
            setIdLastCard(cartesData.length > 0 ? cartesData[cartesData.length - 1].id : null);
            setLoading(false);
        } catch (error) {
            console.error('Fetch error:', error);
        }
    };

    const onGlobalFilterChange = (e) => {
        const value = e.target.value;
        setFilters((prevFilters) => ({
            ...prevFilters,
            'global': { ...prevFilters.global, value },
        }));
        setGlobalFilterValue(value);
    };

    const clearFilters = () => {
        setFilters({
            'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
            'reference': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            'valeur': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] }
        });
        setGlobalFilterValue('');
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
    <button icon="pi pi-arrow-right" onClick={() => { setVisibleRight(true), produitPretEnCours(rowData) }}></button>

    const header = renderHeader();

    const [showMessage, setShowMessage] = useState(false);
    const [showMessageError, setShowMessageError] = useState(false);
    const [formData, setFormData] = useState({});

    const validate = (data) => {
        let errors = {};

        if (!data.valeur) {
            errors.valeur = 'une valeur est requise';
        }

        return errors;
    };

    const convertisseurAzertyQwerty = (value) => {
        let map = {
            '1': '!',
            '2': '@',
            '3': '#',
            '4': '$',
            '5': '%',
            '6': '^',
            '7': '&',
            '8': '*',
            '9': '(',
            '0': ')',
            '@': '2',
            '#': '3',
            '$': '4',
            '%': '5',
            '^': '6',
            '&': '7',
            '*': '8',
            '(': '9',
            ')': '0',
            'a': 'q',
            'z': 'w',
            'q': 'a',
            'w': 'z',
            'A': 'Q',
            'Z': 'W',
            'Q': 'A',
            'W': 'Z',
            "'": '{',
            '^': '[',
            '£': '}',
            '$': ']',
            'M': ':',
            'm': ';',
            ',': 'm',
            '%': "'",
            'ù': "'",
            'µ': '|',
            '*': '\\',
            '?': 'M',
            ',': 'm',
            '.': '<',
            ';': 'm',
            '/': '>',
            ':': '.',
            '§': '?',
            '!': '/',
            'à': '0',
            '&': '1',
            'é': '2',
            '"': '3',
            "'": '4',
            '(': '5',
            '-': '6',
            'è': '7',
            '_': '8',
            "ç": '9',
        };

        return value.split('').map(char => map[char] || char).join('');
    };

    const onSubmit = async (data, form) => {
        let valeurCarte = convertisseurAzertyQwerty(data.valeur);
        data.valeur = valeurCarte;
        setFormData(data);
    
        // $carte->setReference($date->format('dmY') . '00' . $this->carteRepository->nextId());
        let reference = new Date().toLocaleDateString('fr-FR').replace(/\//g, '') + '00' + (idLastCard + 1);
    
        let carte = {
            "reference": reference,
            "valeur": data.valeur,
        };
    
        // Utilisation des paramètres de recherche dans l'URL pour vérifier l'existence de la carte
        let url = new URL(apiUrl + '/cartes');
        url.searchParams.append('valeur', data.valeur);
    
        let carteExists = false;
    
        try {
            let response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    Authorization: 'Bearer ' + props.token,
                },
            });
    
            if (response.ok) {
                let result = await response.json();
                if (result['hydra:member'] && result['hydra:member'].length > 0) {
                    carteExists = true;
                }
            }
        } catch (error) {
            console.error('Error fetching card:', error);
        }
    
        if (carteExists) {
            setShowMessageError(true);
        } else {
            try {
                let postResponse = await fetch(apiUrl + '/cartes', {
                    method: 'POST',
                    headers: {
                        Authorization: 'Bearer ' + props.token,
                        'Content-Type': 'application/ld+json',
                    },
                    body: JSON.stringify(carte),
                });
    
                if (postResponse.ok) {
                    setShowMessage(true);
                    fetchDataCarte();
                } else {
                    console.error('Error creating card:', postResponse.statusText);
                }
            } catch (error) {
                console.error('Error creating card:', error);
            }
        }
    
        form.restart();
    };
    

    const isFormFieldValid = (meta) => !!(meta.touched && meta.error);
    const getFormErrorMessage = (meta) => {
        return isFormFieldValid(meta) && <small className="p-error">{meta.error}</small>;
    };

    // const dialogFooter = <div className="flex justify-content-center"><Button label="OK" className="p-button-text" autoFocus onClick={() => setShowMessage(false)} /></div>;

    
    const dialogFooter = (
        <div className="flex justify-center">
            <Button label="OK" className="p-button-text" autoFocus onClick={() => setShowMessage(false)} />
        </div>
    );    

    const dialogFooterError = (
        <div className="flex justify-center">
            <Button label="OK" className="p-button-text" autoFocus onClick={() => setShowMessageError(false)} />
        </div>
    );

    const DeleteButton = ({ idCarte }) => {
        const [isDeleting, setIsDeleting] = useState(false);

        const deleteProduct = (idCarte) => {
            setIsDeleting(true);
            fetch(apiUrl + `/cartes/${idCarte}`, {
                method: 'DELETE',
                headers: {
                    Authorization: 'Bearer ' + props.token,
                },
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    fetchDataCarte();
                })
                .catch((error) => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    setIsDeleting(false);
                });
            }

            return (
                <button
                    className={`text-slate-800 hover:text-white text-sm bg-white hover:bg-red-400 border border-slate-200 rounded-lg font-medium px-4 py-2 inline-flex space-x-1 items-center ${isDeleting ? 'opacity-50 pointer-events-none' : ''}`}
                    onClick={() => deleteProduct(idCarte)}
                    disabled={isDeleting}
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className="w-6 h-6 hover:stroke-white">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
            );
        };
           

    return (
        <div className="panel space-y-4">
            <div className="flex items-center justify-between">
                <h1 className="text-2xl font-bold">Liste des cartes</h1>
                <button onClick={() => {setVisibleRight(true)}} className="w-40 h-12 bg-white cursor-pointer rounded-xl border border-[#D6BBFB] text-[#D6BBFB] shadow-[inset_0px_-2px_0px_1px_#D6BBFB] group hover:bg-[#D6BBFB] hover:text-white transition duration-300 ease-in-out">
                        <span className="font-medium group-hover:text-white">Ajouter une carte</span>
                </button>
            </div>
        
            <div>
                <DataTable
                    value={cartes}
                    paginator
                    removableSort
                    rows={10}
                    dataKey="id"
                    filters={filters}
                    loading={loading}
                    globalFilterFields={['reference', 'valeur']}
                    header={header}
                    emptyMessage="Aucune carte trouvé"
                >
                    <Column field="reference" header="Référence" filter filterPlaceholder="Recherche par référence carte" sortable />
                    <Column field="valeur" header="Valeur" filter filterPlaceholder="Recherche par valeur" sortable />
                    <Column header="Actions" className="w-1/11" body={(rowData) => (
                    <div className="flex justify-start items-center">
                        {rowData.prets.length === 0 ? <DeleteButton idCarte={rowData.id} /> : null}
                    </div>
                    )} />
                </DataTable>
                <Sidebar 
                visible={visibleRight} 
                position="right" 
                onHide={() => setVisibleRight(false)} 
                className="w-[30%]" 
                style={{ width: '30%' }}
            >
            
                <div className="form-demo">
                    <Dialog
                        visible={showMessage}
                        onHide={() => setShowMessage(false)}
                        position="top"
                        footer={dialogFooter}
                        showHeader={false}
                        breakpoints={{ '960px': '80vw' }}
                        style={{ width: '30vw' }}
                    >
                        <div className="flex flex-col items-center pt-6 px-3">
                            <i className="pi pi-check-circle text-5xl text-green-500"></i>
                            <h5 className="mt-4 text-xl font-semibold text-gray-800">Création de carte réussie</h5>
                            <p className="mt-2 text-center text-gray-600 leading-relaxed">
                                Vous venez de créer une carte de valeur <b>{formData.valeur}</b>
                            </p>
                        </div>
                    </Dialog>
                    <Dialog
                        visible={showMessageError}
                        onHide={() => setShowMessageError(false)}
                        position="top"
                        footer={dialogFooterError}
                        showHeader={false}
                        breakpoints={{ '960px': '80vw' }}
                        style={{ width: '30vw' }}
                    >
                        <div className="flex flex-col items-center pt-6 px-3">
                            <i className="pi pi-check-circle text-5xl text-red-500"></i>
                            <h5 className="mt-4 text-xl font-semibold text-gray-800">Erreur dans la création</h5>
                            <p className="mt-2 text-center text-gray-600 leading-relaxed">
                                La carte de valeur <b>{formData.valeur}</b> existe déjà
                            </p>
                        </div>
                    </Dialog>
                    <div className="flex justify-content-center panel">
                        <div className="card">
                            <h5 className="text-center">Enregistrer une carte</h5>
                            <Form onSubmit={onSubmit} initialValues={{ valeur: '' }} validate={validate} render={({ handleSubmit }) => (
                                <form onSubmit={handleSubmit} className="p-fluid">
                                    <Field name="valeur" render={({ input, meta }) => (
                                        <div className="field">
                                            <span className="p-float-label">
                                                <InputText id="valeur" {...input} autoFocus className="form-input ltr:pl-9 rtl:pr-9 ltr:sm:pr-4 rtl:sm:pl-4 ltr:pr-9 rtl:pl-9 peer sm:bg-transparent bg-gray-100 placeholder:tracking-widest p-inputtext p-component" />
                                                <label htmlFor="valeur" className={classNames({ 'p-error': isFormFieldValid(meta) })}>Valeur</label>
                                            </span>
                                            {getFormErrorMessage(meta)}
                                        </div>
                                    )} />

                                    <Button type="submit" label="Enregistrer" className="mt-2" />
                                </form>
                            )} />
                        </div>
                    </div>
                </div>
            </Sidebar>
            </div>
        </div>
    );
}
