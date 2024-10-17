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
import { Sidebar } from 'primereact/sidebar';
import { Dialog } from 'primereact/dialog';

export default function TableauPret(props) {

    const [prets, setPrets] = useState([]);
    const [loading, setLoading] = useState(true);
    const [globalFilterValue, setGlobalFilterValue] = useState('');
    const [filters, setFilters] = useState(null);
    const [visibleSideBarPret, setVisibleSideBarPret] = useState(false);
    const [produitChoisis ,setProduitChoisis] = useState([]);
    const [etatPret, setEtatPret] = useState(props.pret);
    const [visibleSideBarRelancePret, setvisibleSideBarRelancePret] = useState(false);
    const [relancePretChoisi, setRelancePretChoisi] = useState([]);

    const apiUrl = 'https://' + window.location.host + '/api';

    const urlBase = 'https://' + window.location.host;

    console.log(props.token);

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
        fetchDataPret();
        initFilters();
    }, []);

    const fetchDataPret = () => {
            // fais une requete avec une condition qui est que le pret ne doit pas avoir de date de fin ou que celle ci soit supérieure à la date actuelle
        fetch(apiUrl + '/prets', {
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
            if (props.pret === 'enCours') {
                data['hydra:member'] = data['hydra:member'].filter(pret => pret.dateFin == null || new Date(pret.dateFin) > new Date());
            }
            else if (props.pret === 'pretUser') {
                data['hydra:member'] = data['hydra:member'].filter(pret => pret.user.email === props.user);
            }
            else if (props.pret === 'enRetard') {
                data['hydra:member'] = data['hydra:member'].filter(pret => pret.dateFinPrevue != null && new Date(pret.dateFinPrevue) < new Date());
            }
            const pretData = data['hydra:member'].map(pret => (
                {
                    ...pret,
                    user: pret.user ? pret.user : '',
                    datePret: pret.datePret ? new Date(pret.datePret).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '',
                    dateFin: pret.dateFin ? new Date(pret.dateFin).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '',
                    dateFinPrevue: pret.dateFinPrevue ? new Date(pret.dateFinPrevue).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }) : '',
                   
                }));
                setPrets(pretData);
                console.log(pretData);
                setLoading(false);
            });
    };

    const initFilters = () => {
        setFilters({
            'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
            'user': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            'carte.reference': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
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

    const bodyCarteCorrespondante = (rowData) => {
        return (
            <div className="flex align-items-center gap-2">
                <span>{rowData.carte ? rowData.carte.reference : ''}</span>
            </div>
        );
    };

    const header = renderHeader();

    const affichageInfosProduit = (id, rowData) => {
        for (let i = 0; i < rowData.produit.length; i++) {
            if (rowData.produit[i].id === id) {
                setProduitChoisis(rowData.produit[i]);
            }
        } 
        return null;
    }
    
    const affichageProduit = (rowData) => {
        return (
            <div className="flex flex-col gap-2">
                {rowData.produit.map((prod) => (
                    <button key={prod.id} onClick={() => { affichageInfosProduit(prod.id, rowData); setVisibleSideBarPret(true); }}>
                        <span>{prod.nom}</span>
                    </button>
                ))}
            </div>
        );
    };

    const relancePretChoix = (id, rowData) => {
        const relanceChoisie = rowData.relancePrets.find(relance => relance.id === id);
        if (relanceChoisie) {
            setRelancePretChoisi(relanceChoisie);
            setvisibleSideBarRelancePret(true); // Afficher la sidebar ici lorsque la relance est choisie
        }
    };

    const affichageRelancesPret = (rowData) => {
        return (
            <div>
                {rowData.relancePrets.map((relance, index) => (
                    <div key={index}>
                        <button onClick={() => relancePretChoix(relance.id, rowData)}>
                            {relance.titre}
                        </button>
                    </div>
                ))}
            </div>
        );
    };

    const constructPath = (url, params) => {
        // met un rul absolue pour la redirection
        if (params) {
            return `${url}${params}`;
        }
        return url;
    }; 

    const getButtonClasses = () => {
        switch (etatPret) {
            case 'enRetard':
                return 'rounded-l-lg'; // Ajoutez d'autres classes si nécessaire
            case 'enCours':
                return 'rounded-l-lg'; // Ajoutez d'autres classes si nécessaire
            default:
                return 'rounded-lg'; // Classe par défaut si aucune condition ne correspond
        }
    };
    
        const ModifButton = ({ url, id, color }) => {
            const path = constructPath(url,  id );

            return (
                <a href={path} className="inline-flex items-center space-x-1">
                    <button className={`text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 ${getButtonClasses()} font-medium px-4 py-2`}>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className={`w-6 h-6 ${color}`}>
                            <path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path>
                        </svg>
                    </button>
                </a>
            );
        };

        const relancePretButton = (pret) => {
            // Compter le nombre de relances du prêt actuel
            const relanceIdPret = prets.filter(p => p.id === pret.id)[0].relancePrets.length;
        
            // Créer le message de relance
            const message = `Bonjour. Votre emprunt pour le matériel datant du ${pret.datePret} est arrivé à son terme depuis le ${pret.dateFinPrevue}. Vous êtes priés de le ramener au plus vite.`;
        
            // Créer l'objet relancePret
            const relancePret = {
                contenu: message,
                titre: `Relance n°${relanceIdPret + 1}`,
                createdAt: new Date(),
                pret: `/api/prets/${pret.id}`,
            };
        
            // Fonction pour envoyer la requête POST
            const sendRelancePret = async () => {
                try {
                    const response = await fetch(urlBase + `/relance/ajouter/${pret.id}`, {
                        method: 'POST',
                        body: relancePret,
                    });
        
                    if (!response.ok) {
                        throw new Error('Erreur lors de l\'envoi de la relance');
                    }
                    fetchDataPret();
                    console.log('Relance envoyée avec succès');
                } catch (error) {
                    console.error('Erreur:', error);
                }
            };
        
            return (
                <button
                    onClick={sendRelancePret}
                    className="text-slate-800 hover:text-blue-600 text-xl bg-white hover:bg-slate-100 border border-slate-200 rounded-r-lg font-medium px-4 py-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.5">
                            <path strokeLinejoin="round" d="m7 8l5 3l5-3" />
                            <path d="M10 20H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v6.857" />
                            <path strokeLinejoin="round" d="M22 17.111h-6.3c-3.6 0-3.6 4.889 0 4.889m6.3-4.889L18.85 14M22 17.111l-3.15 3.111" />
                        </g>
                    </svg>
                </button>
            );
        };


        // const buttonCloturePret = () => {
        //     return (
        //         <button className={`text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 ${getButtonClasses()} font-medium px-4 py-2`}>
        //             <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5M16 2v4M8 2v4m-5 4h5m9.5 7.5L16 16.3V14"/><circle cx="16" cy="16" r="6"/></g></svg>
        //         </button>
        //     )
        // };

        const [showMessage, setShowMessage] = useState(false);
        const [selectedPret, setSelectedPret] = useState(null);

        const cloturerPret = async (pret) => {
            console.log(pret);
            try {
                // Mettre à jour la date de fin du prêt à la date actuelle
                const dateFin = new Date().toISOString(); // Convertir en ISO 8601 pour le format de date
        
                // Créer un nouvel objet prêt avec la date de fin mise à jour
                const pretFinal = {
                    dateFin: dateFin,
                    "user" : pret.p.user['@id']
                };
        
                // Envoyer la requête PUT pour mettre à jour le prêt
                const response = await fetch(apiUrl + `/prets/${pret.p.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/merge-patch+json',
                        Authorization: 'Bearer ' + props.token, // Remplacez par votre token d'authentification réel
                    },
                    body: JSON.stringify(pretFinal), // Convertir l'objet JavaScript en JSON pour l'envoi
                });
        
                if (!response.ok) {
                    throw new Error('Erreur lors de la mise à jour du prêt');
                }
                fetchDataPret();
                console.log('Prêt clôturé avec succès');

                if (pret.p.produit.length > 0) {
                    pret.p.produit.forEach(async (produit) => {
                        try {
                            // Mettre à jour l'état du produit à "Disponible"
                            const produitFinal = {
                                etat: "Disponible"
                            };

                            // Envoyer la requête PUT pour mettre à jour le produit
                            const response = await fetch(apiUrl + `/produits/${produit.id}`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/merge-patch+json',
                                    Authorization: 'Bearer ' + props.token, // Remplacez par votre token d'authentification réel
                                },
                                body: JSON.stringify(produitFinal), // Convertir l'objet JavaScript en JSON pour l'envoi
                            });

                            if (!response.ok) {
                                throw new Error('Erreur lors de la mise à jour du produit');
                            }
                            console.log(`Produit ${produit.id} mis à jour avec succès`);
                        } catch (error) {
                            console.error(`Erreur dans la mise à jour du produit ${produit.id}`, error);
                        }
                    });
                }
        
            } catch (error) {
                console.error('Erreur dans la clôture du prêt', error);
            }
            setShowMessage(false);
        };
        
        

        const ButtonCloturePret = (p) => {
            return (
                <div>
                    <button onClick={() => {
                        setShowMessage(true), 
                        setSelectedPret(p);
                    }} className={`text-slate-800 hover:text-blue-600 text-sm bg-white hover:bg-slate-100 border border-slate-200 rounded-r-lg font-medium px-4 py-2`}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.7em" height="1.7em" viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2">
                                <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5M16 2v4M8 2v4m-5 4h5m9.5 7.5L16 16.3V14" />
                                <circle cx="16" cy="16" r="6" />
                            </g>
                        </svg>
                    </button>
                </div>
            );
        };


    
    
    

    return (
        <div className="card">
            <DataTable className="w-full text-sm"
                value={prets}
                removableSort
                paginator
                rows={10}
                dataKey="id"
                filters={filters}
                loading={loading}
                globalFilterFields={['user', 'carte.reference', 'dateDebut', 'dateFin', 'dateFinPrevue', 'commentaire', 'produits']}
                header={header}
                emptyMessage="Aucun prêt trouvé"
            >
                <Column field='user' header="Utilisateur" filter filterPlaceholder='Recherche par user' sortable className="w-1/11" body={(rowData) => {
                    return (
                        <div className="flex flex-col gap-2">
                            <p>{ rowData.user.email}</p>
                        </div>
                    )
                } }/>
                <Column field='carte.reference' header="Carte Correspondante" filter filterPlaceholder='Recherche par référence carte' sortable className="w-1/11" body={bodyCarteCorrespondante} />
                <Column field="datePret" header="Date de début" sortable className="w-1/11" />
                <Column field="dateFinPrevue" header="Date de fin prévue" sortable className="w-1/11" />
                <Column field="dateFin" header="Date de fin" sortable className="w-1/11" />
                <Column field="commentaire" header="Commentaire" sortable className="w-1/11" />
                <Column field="produit" header="Liste des produits" sortable className="w-1/15" body={affichageProduit}/>
                {etatPret === 'enRetard' ? (
                <Column
                    field="relancePrets"
                    header="Relance prêt"
                    className="w-1/11"
                    body={(rowData) => affichageRelancesPret(rowData)}
                />
                ) : null}
                <Column header="Actions" className="w-1/11" body={(rowData) => (
                    props.pret === 'enRetard' ? (
                        <div className="flex justify-start items-center">
                            {ModifButton({ url: '../../pret/editer/', id: rowData.id })}
                            {relancePretButton(rowData)}
                        </div>
                    ) : props.pret === 'enCours' ? (
                        <div className="flex justify-start items-center">
                            {ModifButton({ url: '../../pret/editer/', id: rowData.id })}
                            <ButtonCloturePret p={rowData} />
                        </div>
                    )
                    : (
                        <div className="flex justify-start items-center">
                            {ModifButton({ url: '../../pret/editer/', id: rowData.id })}
                        </div>
                    )
                )} />
            </DataTable>
            <Dialog
                visible={showMessage}
                onHide={() => setShowMessage(false)}
                position="top"
                showHeader={false}
                breakpoints={{ '960px': '80vw' }}
                style={{ width: '30vw' }}
            >
                <div className="flex flex-col items-center pt-6 px-3 space-y-4">
                    <i className="pi pi-exclamation-triangle text-5xl text-red-500"></i>
                    <h5 className="mt-4 text-xl font-semibold text-gray-800">Etes vous sur de vouloir cloturer ce pret ? </h5>
                    <div className="flex space-x-4 ">
                        <button onClick={ () => {
                            cloturerPret(selectedPret, selectedPret.id);
                        }} className="bg-gray-200 hover:bg-red-700 text-gray-800 font-bold py-2 px-4 rounded">
                            Oui
                        </button>
                        <button onClick={() => {
                            setShowMessage(false);
                        } } className="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded mr-2">
                            Non
                        </button>
                    </div>
                </div>
            </Dialog>
            <Sidebar
                visible={visibleSideBarRelancePret} 
                position="right" 
                onHide={() => setvisibleSideBarRelancePret(false)} 
                className="w-[30%]" 
                style={{ width: '30%' }}
            >
                {relancePretChoisi ? (
                    <div className="panel container relative flex flex-col h-full max-w-6xl px-10 mx-auto xl:px-0 mt-5">
                        <h2 className="mb-3 text-3xl font-extrabold leading-tight text-gray-900 text-center">Informations de la Relance</h2>
                        <div className="flex flex-wrap mb-10 -mx-2">
                            <div className="w-full px-2 mb-4">
                                <div className="bg-white border-2 border-indigo-500 rounded-lg">
                                    <div className="p-5">
                                        <h3 className="text-lg font-bold text-gray-800 text-center mb-2">{relancePretChoisi.titre}</h3>
                                        <p className="text-center">{relancePretChoisi.contenu}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ) : null}
            </Sidebar>

            <Sidebar 
                visible={visibleSideBarPret} 
                position="right" 
                onHide={() => setVisibleSideBarPret(false)} 
                className="w-[30%]" 
                style={{ width: '30%' }}>
                {produitChoisis ? (
                    <div className="panel container relative flex flex-col h-full max-w-6xl px-10 mx-auto xl:px-0 mt-5">
                        <h2 className="mb-3 text-3xl font-extrabold leading-tight text-gray-900 text-center">Informations du Produit</h2>
                        <div className="flex flex-wrap mb-10 -mx-2">
                            <div className="w-full px-2 mb-4 sm:w-1/2">
                                <div className="relative h-full ml-0 mr-0 sm:mr-10">
                                    <span className="absolute top-0 left-0 w-full h-full mt-1 ml-1 bg-indigo-500 rounded-lg"></span>
                                    <div className="relative h-full p-5 bg-white border-2 border-indigo-500 rounded-lg flex flex-col justify-center text-center">
                                        <div className="flex items-center justify-center -mt-1">
                                            <h3 className="my-2 ml-3 text-lg font-bold text-gray-800">Nom</h3>
                                        </div>
                                        <p>{ produitChoisis.nom }</p>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full px-2 mb-4 sm:w-1/2">
                                <div class="relative h-full ml-0 mr-0 sm:mr-10">
                                    <span class="absolute top-0 left-0 w-full h-full mt-1 ml-1 bg-purple-500 rounded-lg"></span>
                                    <div class="relative h-full p-5 bg-white border-2 border-purple-500 rounded-lg flex flex-col justify-center text-center">
                                        <div class="flex items-center justify-center -mt-1">
                                            <h3 class="my-2 ml-3 text-lg font-bold text-gray-800">Type de Produit</h3>
                                        </div>
                                        <div className={`flex ${produitChoisis.typeProduit && produitChoisis.typeProduit.length > 1 ? 'justify-between' : 'justify-center'} space-x-2`}>
                                            {produitChoisis.typeProduit ? (
                                                produitChoisis.typeProduit.map((type) => (
                                                    type.nom === 'Immo' ? (
                                                        <span key={type.id} className="btn rounded-lg text-[#66E2AC] border-[#66E2AC] hover:bg-[#66E2AC] hover:text-white py-1 px-3 inline-block text-center">
                                                            Consommable
                                                        </span>
                                                    ) : type.nom === 'Pret' ? (
                                                        <span key={type.id} className="btn rounded-lg text-[#87CEEB] border-[#87CEEB] hover:bg-[#87CEEB] hover:text-white py-1 px-3 inline-block text-center">
                                                            Prêt
                                                        </span>
                                                    ) : type.nom === 'Affectation' ? (
                                                        <span key={type.id} className="btn rounded-lg text-[#FFA500] border-[#FFA500] hover:bg-[#FFA500] hover:text-white py-1 px-3 inline-block text-center">
                                                            Affectation
                                                        </span>
                                                    ) : null
                                                ))
                                            ) : (
                                                <p>Non spécifié</p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full px-2 mb-4 sm:w-1/2">
                                <div class="relative h-full ml-0 mr-0 sm:mr-10">
                                    <span class="absolute top-0 left-0 w-full h-full mt-1 ml-1 bg-blue-400 rounded-lg"></span>
                                    <div class="relative h-full p-5 bg-white border-2 border-blue-400 rounded-lg flex flex-col justify-center text-center">
                                        <div class="flex items-center justify-center -mt-1">
                                            <h3 class="my-2 ml-3 text-lg font-bold text-gray-800">Catégories</h3>
                                        </div>
                                        {produitChoisis.categories ? (
                                            produitChoisis.categories.map((cat) => (
                                                <p key={cat.id}>{cat.nom}</p>
                                            ))
                                        ) : (
                                            <p>Non spécifiées</p>
                                        )}
                                    </div>
                                </div>
                            </div>
                            <div class="w-full px-2 mb-4 sm:w-1/2">
                                <div class="relative h-full ml-0 mr-0 sm:mr-10">
                                    <span class="absolute top-0 left-0 w-full h-full mt-1 ml-1 bg-yellow-400 rounded-lg"></span>
                                    <div class="relative h-full p-5 bg-white border-2 border-yellow-400 rounded-lg flex flex-col justify-center text-center">
                                        <div class="flex items-center justify-center -mt-1">
                                            <h3 class="my-2 ml-3 text-lg font-bold text-gray-800">Marque</h3>
                                        </div>
                                        {produitChoisis.marque ? (
                                            <p>{produitChoisis.marque.nom}</p>
                                        ) : (
                                            <p>Non spécifiée</p>
                                        )}
                                    </div>
                                </div>
                            </div>
                            <div class="w-full px-2 mb-4 sm:w-1/2">
                                <div class="relative h-full ml-0 mr-0 sm:mr-10">
                                    <span class="absolute top-0 left-0 w-full h-full mt-1 ml-1 bg-green-500 rounded-lg"></span>
                                    <div class="relative h-full p-5 bg-white border-2 border-green-500 rounded-lg flex flex-col justify-center text-center">
                                        <div class="flex items-center justify-center -mt-1">
                                            <h3 class="my-2 ml-3 text-lg font-bold text-gray-800">Quantité</h3>
                                        </div>
                                        <p>{ produitChoisis.quantite }</p>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full px-2 mb-4 sm:w-1/2">
                                <div class="relative h-full ml-0 mr-0 sm:mr-10">
                                    <span class="absolute top-0 left-0 w-full h-full mt-1 ml-1 bg-orange-500 rounded-lg"></span>
                                    <div class="relative h-full p-5 bg-white border-2 border-orange-500 rounded-lg flex flex-col justify-center text-center overflow-hidden">
                                        <div class="flex items-center justify-center -mt-1">
                                            <h3 class="my-2 ml-3 text-lg font-bold text-gray-800">Référence</h3>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 mt-4">
                                            <div class="font-bold text-gray-700 break-words">Référence Fabricant :</div>
                                            <div class="text-gray-900 break-words">{produitChoisis.refFabricant}</div>
                                            <div class="font-bold text-gray-700 break-words">Référence Interne :</div>
                                            <div class="text-gray-900 break-words">{produitChoisis.refInterne}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                               
                ) : null}

            </Sidebar>
        </div>
    );
}
