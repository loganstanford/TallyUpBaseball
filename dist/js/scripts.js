function recommendPosition(pos, lineup) {
    var d = document.createElement('html');
    var table = $(`#hitters-${pos}`).DataTable().row(0).column(0).data();
    var num = 1; // Tracks number of Outfielders
    var result = ''
    for (let i = 0; i < table.length; i++) {
        if (table[i].includes('text-danger')) {
            continue; // Skip player if they are not in lineup
        }
        else if (table[i].includes('injury-status')) {
            continue; // Skip if player has injury news
        }
        else {
            d.innerHTML = table[i];
            var name = d.getElementsByTagName('a')[0].innerHTML;
            var team = d.getElementsByTagName('span')[0].innerHTML.substring(0,3);
            if (lineup.includes(name)) {
                continue;
            }
            result += pos + ": " + name + " - " + team + "<br>";
            
            if (pos == 'OF' && num < 3) {
                num++;
                continue;
            }
            return result;
        }
    }
    // If it's made it this far, then no player eligible. Return top performing player
    d.innerHTML = table[0];
    var name = d.getElementsByTagName('a')[0].innerHTML;
    var team = d.getElementsByTagName('span')[0].innerHTML.substring(0,3);
    var result = pos + ": " + name + " - " + team;
    return result;
}

function recommendLineup() {
    let positions = ['C', '1B', '2B', 'SS', '3B', 'OF'];
    var result = '';
    for (let i = 0; i < positions.length; i++) {
        result += recommendPosition(positions[i], result);
    }

    // Get DH
    // Get all hitters table
    var hitters = $('#hitters-all').DataTable().row(0).column(0).data();
    for (i = 0; i < hitters.length; i++) {
        var start = hitters[i].indexOf(')">') + 3;
        var end = hitters[i].indexOf('</a>');
        var name = hitters[i].substring(start, end);
        // Check if not in lineup
        if (hitters[i].includes('text-danger')) {
            continue;
        }
        else if (hitters[i].includes('injury-status')) {
            continue; // Skip if player has injury news
        }
        // Check if result includes name
        else if (result.includes(name)) {
            //console.log(`Result includes ${hitters[i].substring(start, end)}`);
            continue;
        }
        else {
            //console.log(`Result doesn't include ${name}`);
            var team = hitters[i].substring(hitters[i].indexOf('team">') + 6, hitters[i].indexOf(' - </span>'))
            result += 'DH: ' + name + ' - ' + team + '<br>';
            break;
        }
    }
    

    // Pitching
    var p = document.createElement('html');
    var p_team  = $('#pitching').DataTable().row(0).column(0).data()[0];
    var pitching = "P: " + p_team;

    //$('#lineup-content > #lineup-body')[0].innerHTML = catcher + "<br>" + firstbase + "<br>" +secondbase+"<br>"+short+"<br>"+thirdbase+"<br>"+outfield_1+"<br>"+outfield_2+"<br>"+outfield_3+"<br>"+pitching;
    $('#lineup-content > #lineup-body')[0].innerHTML = result + pitching;
    $('modal-lineup').modal('show');

};

function copyToClipboard() {
    navigator.clipboard.writeText($('#lineup-body')[0].innerText);
    Swal.fire({
        icon: 'success',
        title: 'Lineup copied to clipboard!',
        showConfirmButton: false,
        timer: 1500
})

}

function showModal(id) {
    let modal_id = "#" + id
    $(modal_id).modal('show')
};

$(function() {

    function updateButton() {
        const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
        });

        if (params.start_date != null) {
            var start = params.start_date;
        }
        else {
            var start = '2023-03-30';
        }
        if (params.end_date != null) {
            var end = params.end_date;
        }
        else {
            var end = moment().format('YYYY-MM-DD');
        }

        if (start == moment().format('YYYY-MM-DD')) {
            $('#daterange-btn span').html("Today");
        }
        else if (start == moment().subtract(1, 'days').format('YYYY-MM-DD')) {
            $('#daterange-btn span').html("Yesterday");
        }
        else if (start == moment().subtract(6, 'days').format('YYYY-MM-DD')) {
            $('#daterange-btn span').html("Last 7");
        }
        else if (start == moment().subtract(14, 'days').format('YYYY-MM-DD')) {
            $('#daterange-btn span').html("Last 15");
        }
        else if (start == moment().subtract(29, 'days').format('YYYY-MM-DD')) {
            $('#daterange-btn span').html("Last 30");
        }
        else if (start == moment().startOf('month').format('YYYY-MM-DD') && end == moment().endOf('month').format('YYYY-MM-DD')) {
            $('#daterange-btn span').html("This Month");
        }
        else if (start == moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD') && end == moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD')) {
            $('#daterange-btn span').html("Last Month");
        }
        else if (start == '2023-03-30' && end == moment().format('YYYY-MM-DD')) {
            $('#daterange-btn span').html("Year to date");
        }
        else {
            $('#daterange-btn span').html(moment(start, 'YYYY-MM-DD').format('MMMM D') + ' - ' + moment(end, 'YYYY-MM-DD').format('MMMM D'));
        }
        /* 'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        'Year to date': [moment('2023-03-30', 'YYYY-MM-DD'), moment()] */
    }

    function getStart() {
        const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
        });

        if (params.start_date != null) {
            var start = moment(params.start_date, 'YYYY-MM-DD');
        }
        else {
            var start = moment('2023-03-30', 'YYYY-MM-DD');
        }
        return start;
    }
    function getEnd() {
        const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
        });

        if (params.end_date != null) {
            var end = moment(params.end_date, 'YYYY-MM-DD');
        }
        else {
            var end = moment();
        }
        return end;
    }

    updateButton();

    //var start = moment().subtract(29, 'days');
    var start = getStart();
    
    //var end = moment();
    var end = getEnd();


    function cb(start, end) {
        // Default option if no start of end
        $('#daterange-btn span').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
        //var url = "http://tallyfantasy.com/myteam.php"+'?';
        var url = '?';
        var startDate = start ? start.format('YYYY-MM-DD') : '';
        var endDate = end ? end.format('YYYY-MM-DD') : '';
        var params = {};
        if(startDate && endDate){
            params.start_date =  startDate;
            params.end_date =  endDate;
        }
        url+= jQuery.param( params );
        location.href = url
    }

    $('#daterange-btn').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 15 Days': [moment().subtract(14, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        'Year to date': [moment('2023-03-30', 'YYYY-MM-DD'), moment()]
        }
    }, cb);

/*         function (start, end, label) {
        var url = "http://tallyfantasy.com/myteam.php"+'?';
        var startDate = start ? start.format('YYYY-MM-DD') : '';
        var endDate = end ? end.format('YYYY-MM-DD') : '';
        var params = {};
        if(startDate && endDate){
            params.start_date =  startDate;
            params.end_date =  endDate;
        }
        url+= jQuery.param( params );
        location.href = url
    } */
    
});

$(function () {
    $('.bat-hand').tooltip({
        placement: 'top'
    })
});

$(window).on('load', function() {
    $('#loading-overlay').fadeOut('slow');
  });
