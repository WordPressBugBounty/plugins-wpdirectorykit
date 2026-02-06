function removeDropdowns() {
    let datalist = document.querySelectorAll('.autocomplete-dropdown');
    datalist.forEach(element => {
        element.remove();
    });
}

function wdkAutosuggestionhandleDocumentClick(event) {
    if (!event.target.closest('.autocomplete-dropdown')) {
        removeDropdowns();
    }
}

// Attach the event listener
document.addEventListener('click', wdkAutosuggestionhandleDocumentClick);

const wdk_autosuggestion_rest_countries = async (selector = '') => {
        const input = selector.get(0);
    
        if (!input) {
            console.error('Input element not found');
            return;
        }
    
        const inputValue = input.value.toLowerCase();
        let datalist = document.getElementById(input.getAttribute('list'));
    
        // Create or select the custom datalist
        if (!datalist) {
            datalist = document.createElement('div');
            let listId = input.getAttribute('list');
    
            // Generate a random ID if not set
            if (!listId) {
                listId = 'datalist-' + Math.random().toString(36).substr(2, 9);
                input.setAttribute('list', listId);
            }
    
            datalist.id = listId;
            datalist.style.position = 'absolute';
            datalist.style.zIndex = '1000';
            datalist.classList.add('autocomplete-dropdown');
            document.body.appendChild(datalist);
        }
    
        // Clear previous suggestions
        datalist.innerHTML = '';
    
        if (inputValue.length > 2) {
            const response = await fetch('https://restcountries.com/v3.1/all');
            const countries = await response.json();
    
            const filteredCountries = countries.filter(country => 
                country.name.common.toLowerCase().startsWith(inputValue)
            );
    
            filteredCountries.forEach(country => {
                const option = document.createElement('div');
                option.innerHTML = country.name.common;
                option.classList.add('autocomplete-item');
    
                // Handle selection
                option.addEventListener('click', function() {
                    input.value = country.name.common;
                    datalist.innerHTML = ''; // Clear suggestions after selection
                    datalist.remove(); // Remove datalist after selection
                });
    
                datalist.appendChild(option);
            });
    
            // Position the datalist below the input
            const rect = input.getBoundingClientRect();
            datalist.style.left = `${rect.left}px`;
            datalist.style.top = `${rect.bottom + window.scrollY}px`;
            datalist.style.width = `${rect.width}px`;
        }
};

var jqxhr = null;
const wdk_autosuggestion_db_field = async (selector = '', field_id = '') => {
        const input = selector.get(0);

        if (!input) {
            console.error('Input element not found');
            return;
        }
    
        const inputValue = input.value.toLowerCase();
        let datalist = document.getElementById(input.getAttribute('list'));
    
        // Create or select the custom datalist
        if (!datalist) {
            datalist = document.createElement('div');
            let listId = input.getAttribute('list');
    
            // Generate a random ID if not set
            if (!listId) {
                listId = 'datalist-' + Math.random().toString(36).substr(2, 9);
                input.setAttribute('list', listId);
                input.setAttribute('autocomplete', 'off');
            }

            datalist.id = listId;
            datalist.style.position = 'absolute';
            datalist.style.zIndex = '1000';
            datalist.classList.add('autocomplete-dropdown');
            document.body.appendChild(datalist);
        }
    
        // Clear previous suggestions
        datalist.innerHTML = '';
    
        if (inputValue.length > 2) {

                
            // Assign handlers immediately after making the request,
            // and remember the jqxhr object for this request
            if(jqxhr != null) {
                jqxhr.abort();
            }

            var data = {
                "action": 'wdk_public_action',
                "page": 'wdk_frontendajax',
                "function": 'suggestion_db_field',
                "search": inputValue,
                "field_id": field_id
            };

            jqxhr = jQuery.post( script_parameters.ajax_url, data, function(data) {
                if(data.success && data.results.length) {
                    data.results.forEach(value => {
                        const option = document.createElement('div');
                        option.innerHTML = value;
                        option.classList.add('autocomplete-item');
            
                        // Handle selection
                        option.addEventListener('click', function() {
                            input.value = value;
                            datalist.innerHTML = ''; // Clear suggestions after selection
                            datalist.remove(); // Remove datalist after selection
                        });
            
                        datalist.appendChild(option);
                    });
                }
            }).always(function(){
            })
            .fail(function() {
            });
    
            // Position the datalist below the input
            const rect = input.getBoundingClientRect();
            datalist.style.left = `${rect.left}px`;
            datalist.style.top = `${rect.bottom + window.scrollY}px`;
            datalist.style.width = `${rect.width}px`;
        }
};

const wdk_autosuggestion_db_locations = async (selector = '', field_id = '') => {
        const input = selector.get(0);

        if (!input) {
            console.error('Input element not found');
            return;
        }
    
        const inputValue = input.value.toLowerCase();
        let datalist = document.getElementById(input.getAttribute('list'));
    
        // Create or select the custom datalist
        if (!datalist) {
            datalist = document.createElement('div');
            let listId = input.getAttribute('list');
    
            // Generate a random ID if not set
            if (!listId) {
                listId = 'datalist-' + Math.random().toString(36).substr(2, 9);
                input.setAttribute('list', listId);
                input.setAttribute('autocomplete', 'off');
            }
    
            datalist.id = listId;
            datalist.style.position = 'absolute';
            datalist.style.zIndex = '1000';
            datalist.classList.add('autocomplete-dropdown');
            document.body.appendChild(datalist);
        }
    
        // Clear previous suggestions
        datalist.innerHTML = '';
    
        if (inputValue.length > 2) {

                
            // Assign handlers immediately after making the request,
            // and remember the jqxhr object for this request
            if(jqxhr != null) {
                jqxhr.abort();
            }

            var data = {
                "action": 'wdk_public_action',
                "page": 'wdk_frontendajax',
                "function": 'suggestion_locations',
                "search": inputValue,
            };

            jqxhr = jQuery.post( script_parameters.ajax_url, data, function(data) {
                if(data.success && data.results.length) {
                    data.results.forEach(value => {
                        const option = document.createElement('div');
                        option.innerHTML = value;
                        option.classList.add('autocomplete-item');
            
                        // Handle selection
                        option.addEventListener('click', function() {
                            input.value = value;
                            datalist.innerHTML = ''; // Clear suggestions after selection
                            datalist.remove(); // Remove datalist after selection
                        });
            
                        datalist.appendChild(option);
                    });
                }
            }).always(function(){
            })
            .fail(function() {
            });
    
            // Position the datalist below the input
            const rect = input.getBoundingClientRect();
            datalist.style.left = `${rect.left}px`;
            datalist.style.top = `${rect.bottom + window.scrollY}px`;
            datalist.style.width = `${rect.width}px`;
        }
};

const wdk_autosuggestion_db_categories = async (selector = '', field_id = '') => {
        const input = selector.get(0);

        if (!input) {
            console.error('Input element not found');
            return;
        }
    
        const inputValue = input.value.toLowerCase();
        let datalist = document.getElementById(input.getAttribute('list'));
    
        // Create or select the custom datalist
        if (!datalist) {
            datalist = document.createElement('div');
            let listId = input.getAttribute('list');
    
            // Generate a random ID if not set
            if (!listId) {
                listId = 'datalist-' + Math.random().toString(36).substr(2, 9);
                input.setAttribute('list', listId);
                input.setAttribute('autocomplete', 'off');
            }
    
            datalist.id = listId;
            datalist.style.position = 'absolute';
            datalist.style.zIndex = '1000';
            datalist.classList.add('autocomplete-dropdown');
            document.body.appendChild(datalist);
        }
    
        // Clear previous suggestions
        datalist.innerHTML = '';
    
        if (inputValue.length > 2) {

                
            // Assign handlers immediately after making the request,
            // and remember the jqxhr object for this request
            if(jqxhr != null) {
                jqxhr.abort();
            }

            var data = {
                "action": 'wdk_public_action',
                "page": 'wdk_frontendajax',
                "function": 'suggestion_categories',
                "search": inputValue,
            };

            jqxhr = jQuery.post( script_parameters.ajax_url, data, function(data) {
                if(data.success && data.results.length) {
                    data.results.forEach(value => {
                        const option = document.createElement('div');
                        option.innerHTML = value;
                        option.classList.add('autocomplete-item');
            
                        // Handle selection
                        option.addEventListener('click', function() {
                            input.value = value;
                            datalist.innerHTML = ''; // Clear suggestions after selection
                            datalist.remove(); // Remove datalist after selection
                        });
            
                        datalist.appendChild(option);
                    });
                }
            }).always(function(){
            })
            .fail(function() {
            });
    
            // Position the datalist below the input
            const rect = input.getBoundingClientRect();
            datalist.style.left = `${rect.left}px`;
            datalist.style.top = `${rect.bottom + window.scrollY}px`;
            datalist.style.width = `${rect.width}px`;
        }
};