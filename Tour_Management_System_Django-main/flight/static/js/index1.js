
document.addEventListener("DOMContentLoaded", () => {
    document.querySelector("#flight-from").addEventListener("input", event => {
        flight_from(event);
    });

});


function showplaces(input) {
    let box = input.parentElement.querySelector(".places_box");
    box.style.display = 'block';
}

function hideplaces(input) {
    let box = input.parentElement.querySelector(".places_box");
    setTimeout(() => {
        box.style.display = 'none';
    }, 300);
}

function selectplace(option) {
    let input = option.parentElement.parentElement.querySelector('input[type=text]');
    input.value = option.dataset.value.toUpperCase();
    input.dataset.value = option.dataset.value;
}



function flight_from(event, focus=false) {
    let input = event.target;
    let list = document.querySelector('#places_from');
    showplaces(input);
    if(!focus) {
        input.dataset.value = '';
    }
    if(input.value.length > 0) {
        fetch('query/places/'+input.value)
        .then(response => response.json())
        .then(places => {
            list.innerHTML = '';
            places.forEach(element => {
                let div = document.createElement('div');
                div.setAttribute('class', 'each_places_from_list');
                div.classList.add('places__list');
                div.setAttribute('onclick', "selectplace(this)");
                div.setAttribute('data-value', element.code);
                div.innerText = `${element.city} (${element.country})`;
                list.append(div);
            });
        });
    }
}

function trip_type() {
    document.querySelectorAll('.trip-type').forEach(type => {
        if(type.checked) {
            if(type.value === "1") {
                document.querySelector('#return_date').value = '';
                document.querySelector('#return_date').disabled = true;
            }
            else if(type.value === "2") {
                document.querySelector('#return_date').disabled = false;
            }
        }
    })
}

function flight_search() {
    if(!document.querySelector("#flight-from").dataset.value) {
        alert("Please select flight origin.");
        return false;
    }


}