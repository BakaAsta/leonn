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
import { Form, Field } from 'react-final-form';
import { Dialog } from 'primereact/dialog';
import { classNames } from 'primereact/utils';

export default function TableauCategorie(props) {
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [globalFilterValue, setGlobalFilterValue] = useState('');
    const [filters, setFilters] = useState({
        'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
        'nom': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] }
    });

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
        fetchDataCategorie();
    }, []);

    const fetchDataCategorie = async () => {
        try {
            const response = await fetch(apiUrl + '/categories', {
                headers: {
                    Authorization: 'Bearer ' + props.token,
                },
            });
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            const data = await response.json();
            const categoriesData = data['hydra:member'].map(categorie => ({
                ...categorie,
                id: categorie.id,
                nom: categorie.nom,  // Assurez-vous que 'nom' est le champ correct dans votre réponse API
            }));
            setCategories(categoriesData);
            console.log(categoriesData);
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
            'nom': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
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

    const header = renderHeader();

    const SuppButton = ({ id }) => {
        const [isDeleting, setIsDeleting] = useState(false);
    
        const deleteMarque = (id) => {
            setIsDeleting(true);
            fetch(`/api/categories/${id}`, {
                method: 'DELETE',
                headers: {
                    Authorization: 'Bearer ' + props.token,
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                fetchDataCategorie();
            })
            .catch(error => {
                console.error('Error deleting entity:', error);
            })
            .finally(() => {
                setIsDeleting(false);
            });
        };
    
        return (
            <button
                onClick={() => deleteMarque(id)}
                disabled={isDeleting}
                className={`text-slate-800 hover:text-white text-sm bg-white hover:bg-red-400 border border-slate-200 rounded-r-lg font-medium px-4 py-2 inline-flex space-x-1 items-center ${isDeleting ? 'opacity-50 pointer-events-none' : ''}`}
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className="w-6 h-6 hover:stroke-white">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
        );
    };

    const [visibleCreationCategorie, setVisibleCreationCategorie] = useState(false);
    
    const [showMessage, setShowMessage] = useState(false);
    const [showMessageError, setShowMessageError] = useState(false);
    const [formData, setFormData] = useState({});

    const [typeForm, setTypeForm] = useState(false);

    const [selectedCategorie, setSelectedCategorie] = useState(false);


    // Validation function for category form data
    const validateCategory = (data) => {
        let errors = {};
        if (!data.category) {
            errors.category = 'Le nom de la catégorie est requis';
        }
        return errors;
    };

    // Enregistrer handler for category form
    const onSubmitCategory = async (data, form) => {
        setFormData(data);


        let category = { 
            "nom": data.category 
        };

        try {
            let url = apiUrl + '/categories';

            // Check if category already exists
            let response = await fetch(url, {
                method: 'GET',
                headers: {
                    Authorization: 'Bearer ' + props.token,
                },
            });

            if (response.ok) {
                let result = await response.json();
                if (result['hydra:member'] && result['hydra:member'].some(item => item.nom === data.category)) {
                    if (typeForm == 'creation') {
                    setShowMessageError(true);
                    }
                    else {
                        setShowMessageErrorEdition(true);
                    }
                    return;
                }
            }

            // If category does not exist, create it

            let postResponse;

            if (typeForm === 'creation') {
            postResponse = await fetch(url, {
                method: 'POST',
                headers: {
                    Authorization: 'Bearer ' + props.token,
                    'Content-Type': 'application/ld+json',
                },
                body: JSON.stringify(category),
            });
        }
        else {
            let id = selectedCategorie.id;
            if (selectedCategorie && id) {
                postResponse = await fetch(apiUrl + `/categories/${id}`, {
                    method: 'PATCH',
                    headers: {
                        Authorization: 'Bearer ' + props.token,
                        'Content-Type': 'application/merge-patch+json',
                    },
                    body: JSON.stringify(category),
                });
            }
        }
            if (postResponse.ok) {
                if (typeForm == 'creation') {       
                setShowMessage(true);
                } 
                else {
                    setShowMessageEdition(true);
                }
                fetchDataCategorie();
            } else {
                console.error('Error creating category:', postResponse.statusText);
            }
        } catch (error) {
            console.error('Error creating category:', error);
        }

        form.restart();
    };

    // Check if form field is valid
    const isFormFieldValid = (meta) => !!(meta.touched && meta.error);

    // Get form error message
    const getFormErrorMessage = (meta) => {
        return isFormFieldValid(meta) && <small className="p-error">{meta.error}</small>;
    };

    // Dialog footer for success message
    const dialogFooter = (
        <div className="flex justify-center">
            {typeForm == 'creation' ? (
            <Button label="OK" className="p-button-text" autoFocus onClick={() => setShowMessage(false)} /> 
        ) : (
                <Button label="OK" className="p-button-text" autoFocus onClick={() => setShowMessageEdition(false)} />
            )}
        </div>
    );

    // Dialog footer for error message
    const dialogFooterError = (
        <div className="flex justify-center">
            {typeForm === 'creation' ? (
            <Button label="OK" className="p-button-text" autoFocus onClick={() => setShowMessageError(false)} /> 
            ) : (
                <Button label="OK" className="p-button-text" autoFocus onClick={() => setShowMessageErrorEdition(false)} />
            )}
        </div>
    );

    const ModifButton = (categorie) => {    
        return (
            <button onClick={() =>  {
                setVisibleSideBarEdition(true);
                setSelectedCategorie(categorie.categorie);
                setTypeForm('edition');
            }} className={`text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 rounded-l-lg font-medium px-4 py-2`}>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className={`w-6 h-6 `}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path>
                </svg>
            </button>
        );
    };

    const [sideBarEditionCategorie, setVisibleSideBarEdition] = useState(false);
    const [showMessageEdition, setShowMessageEdition] = useState(false);
    const [showMessageErrorEdition, setShowMessageErrorEdition] = useState(false);

    return (
        <div className="panel space-y-4">
            <div className="flex items-center justify-between">
                <h1 className="text-2xl font-bold">{props.titre}</h1>
                    <button onClick={() => {
                        setVisibleCreationCategorie(true);
                        setTypeForm('creation');
                    } } className="w-40 h-12 bg-white cursor-pointer rounded-xl border border-[#D6BBFB] text-[#D6BBFB] shadow-[inset_0px_-2px_0px_1px_#D6BBFB] group hover:bg-[#D6BBFB] hover:text-white transition duration-300 ease-in-out">
                        <span className="font-medium group-hover:text-white">{ props.titreBouton }</span>
                    </button>
            </div>
            <div className="card">
                <DataTable
                    value={categories}
                    paginator
                    removableSort
                    rows={10}
                    dataKey="id"
                    filters={filters}
                    loading={loading}
                    globalFilterFields={['nom']}
                    header={header}
                    emptyMessage="Aucune catégorie trouvée"
                >
                    <Column field="nom" header="Nom" filter filterPlaceholder="Recherche par nom de catégorie" sortable />
                    <Column header="Actions" className="w-1/11" body={ (rowData) => {
                        return (
                            <div>
                                <ModifButton categorie={rowData} />
                                {rowData.produits.length === 0 ? <SuppButton id={rowData.id} onDelete={true} /> : null}
                            </div>
                        );
                    } }/>
                </DataTable>
                <Sidebar
                    visible={visibleCreationCategorie}
                    position="right"
                    onHide={() => setVisibleCreationCategorie(false)}
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
                                    <h5 className="mt-4 text-xl font-semibold text-gray-800">Création de catégorie réussie</h5>
                                    <p className="mt-2 text-center text-gray-600 leading-relaxed">
                                        Vous venez de créer une catégorie nommée <b>{formData.category}</b>
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
                                    <i className="pi pi-times-circle text-5xl text-red-500"></i>
                                    <h5 className="mt-4 text-xl font-semibold text-gray-800">Erreur dans la création</h5>
                                    <p className="mt-2 text-center text-gray-600 leading-relaxed">
                                        La catégorie de nom <b>{formData.category}</b> existe déjà
                                    </p>
                                </div>
                            </Dialog>
                            <div className="flex justify-content-center panel">
                                <div className="card">
                                    <h5 className="text-center">Enregistrer une catégorie</h5>
                                    <Form
                                        onSubmit={onSubmitCategory}
                                        initialValues={{ category: '' }}
                                        validate={validateCategory}
                                        render={({ handleSubmit }) => (
                                            <form onSubmit={handleSubmit} className="p-fluid">
                                                <Field name="category" render={({ input, meta }) => (
                                                    <div className="field">
                                                        <span className="p-float-label">
                                                            <InputText
                                                                id="category"
                                                                {...input}
                                                                autoFocus
                                                                className="form-input ltr:pl-9 rtl:pr-9 ltr:sm:pr-4 rtl:sm:pl-4 ltr:pr-9 rtl:pl-9 peer sm:bg-transparent bg-gray-100 placeholder:tracking-widest p-inputtext p-component"
                                                            />
                                                            <label htmlFor="category" className={classNames({ 'p-error': isFormFieldValid(meta) })}>Catégorie</label>
                                                        </span>
                                                        {getFormErrorMessage(meta)}
                                                    </div>
                                                )} />
                                                <Button type="submit" label="Enregistrer" className="mt-2" />
                                            </form>
                                        )}
                                    />
                                </div>
                            </div>
                        </div>
                    </Sidebar>
                    <Sidebar
                    visible={sideBarEditionCategorie}
                    position="right"
                    onHide={() => setVisibleSideBarEdition(false)}
                    className="w-[30%]" 
                    style={{ width: '30%' }}
                    >
                        <div className="form-demo">
                            <Dialog
                                visible={showMessageEdition}
                                onHide={() => setShowMessageEdition(false)}
                                position="top"
                                footer={dialogFooter}
                                showHeader={false}
                                breakpoints={{ '960px': '80vw' }}
                                style={{ width: '30vw' }}
                            >
                                <div className="flex flex-col items-center pt-6 px-3">
                                    <i className="pi pi-check-circle text-5xl text-green-500"></i>
                                    <h5 className="mt-4 text-xl font-semibold text-gray-800">Edition de catégorie réussie</h5>
                                    <p className="mt-2 text-center text-gray-600 leading-relaxed">
                                        Vous venez de modifier une catégorie nommée <b>{formData.category}</b>
                                    </p>
                                </div>
                            </Dialog>
                            <Dialog
                                visible={showMessageErrorEdition}
                                onHide={() => setShowMessageErrorEdition(false)}
                                position="top"
                                footer={dialogFooterError}
                                showHeader={false}
                                breakpoints={{ '960px': '80vw' }}
                                style={{ width: '30vw' }}
                            >
                                <div className="flex flex-col items-center pt-6 px-3">
                                    <i className="pi pi-times-circle text-5xl text-red-500"></i>
                                    <h5 className="mt-4 text-xl font-semibold text-gray-800">Erreur dans l'édition</h5>
                                    <p className="mt-2 text-center text-gray-600 leading-relaxed">
                                        La catégorie de nom <b>{formData.category}</b> existe déjà
                                    </p>
                                </div>
                            </Dialog>
                            <div className="flex justify-content-center panel">
                                <div className="card">
                                    <h5 className="text-center">Editer une catégorie</h5>
                                    <Form
                                        onSubmit={onSubmitCategory}
                                        initialValues={{ category: selectedCategorie ? selectedCategorie.nom : null }}
                                        validate={validateCategory}
                                        render={({ handleSubmit }) => (
                                            <form onSubmit={handleSubmit} className="p-fluid">
                                                <Field name="category" render={({ input, meta }) => (
                                                    <div className="field">
                                                        <span className="p-float-label">
                                                            <InputText
                                                                id="category"
                                                                {...input}
                                                                autoFocus
                                                                className="form-input ltr:pl-9 rtl:pr-9 ltr:sm:pr-4 rtl:sm:pl-4 ltr:pr-9 rtl:pl-9 peer sm:bg-transparent bg-gray-100 placeholder:tracking-widest p-inputtext p-component"
                                                            />
                                                            <label htmlFor="category" className={classNames({ 'p-error': isFormFieldValid(meta) })}>Catégorie</label>
                                                        </span>
                                                        {getFormErrorMessage(meta)}
                                                    </div>
                                                )} />
                                                <Button type="submit" label="Enregistrer" className="mt-2" />
                                            </form>
                                        )}
                                    />
                                </div>
                            </div>
                        </div>
                    </Sidebar>
            </div>
        </div>
    );
}
