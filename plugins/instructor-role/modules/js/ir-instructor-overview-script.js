jQuery( document ).ready(
	function(){
		createCoursePieChart( ir_data.chart_data );
		createEarningsDonutChart( ir_data.earnings );

		jQuery( '.ir-instructor-overview-container #instructor-courses-select' ).on(
			'change',
			function(){
				jQuery( '.ir-course-reports .ir-ajax-overlay' ).show();
				$select = jQuery( this );
				jQuery.ajax(
					{
						type: 'post',
						dataType: 'JSON',
						url: ir_data.ajax_url,
						data: {
							action : 'ir-update-course-chart',
							course_id : $select.val()
						},
						success: function(chart_data) {
							jQuery( '.ir-course-reports .ir-ajax-overlay' ).hide();
							createCoursePieChart( chart_data );
						}
					}
				);
			}
		);

		// Setup Datatables
		if ( ! jQuery( '.ir-assignments-table .ir-no-data-found' ).length ) {
			jQuery( '.ir-assignments-table' ).DataTable(
				{
					'language' : {
						'paginate' : {
							'previous' : '',
							'next' : ''
						}
					},
					"columnDefs": [
					{ "width": "20%", "targets": 3 }
					],
					"order": [ 3, 'desc' ]
				}
			);
		}
	}
);

function createCoursePieChart(chart_data)
{
	// jQuery('#ir-course-pie-chart-div').empty();
	var not_started_per = chart_data.not_started;
	var in_progress_per = chart_data.in_progress;
	var completed_per   = chart_data.completed;
	var graph_heading   = chart_data.title;

	jQuery( '#ir-course-pie-chart-div' ).highcharts(
		{
			credits: { enabled: false },
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				height: 250,
				backgroundColor: 'transparent',
			},
			title: {
				// text: 'Browser market shares at a specific website, 2014'
				text: undefined
			},
			tooltip: {
				pointFormat: '{point.y} : <b>{point.percentage:.1f}%</b>',
				valueSuffix: chart_data.default_user_value
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false
					},
					showInLegend: true
				}
			},
			colors: ir_data.colors,
			series: [ {
				type: 'pie',
				name: chart_data.default_course_chart_name,
				data: [
					{
						name: chart_data.not_started_label,
						y: parseFloat( not_started_per ),
						sliced: false,
						selected: true
				},
					[ chart_data.in_progress_label, parseFloat( in_progress_per ) ],
					[ chart_data.completed_label, parseFloat( completed_per ) ]
				],
				// size: '80%'
			} ]
		}
	);

	jQuery( '.ir-tab-links' ).on(
		'click',
		function(){
			var selector = jQuery( this ).data( 'tab' );
			jQuery( '.ir-tab-content' ).hide();
			jQuery( '.ir-tab-links' ).removeClass( 'tab-active' );
			jQuery( this ).addClass( 'tab-active' );
			jQuery( '#' + selector ).show().addClass( 'tab-active' );
		}
	);
}

function createEarningsDonutChart(earnings)
{
	var paid_per      = earnings.paid;
	var un_paid_per   = earnings.unpaid;
	var total         = earnings.total;
	var graph_heading = earnings.title;

	jQuery( '#ir-earnings-pie-chart-div' ).highcharts(
		{
			credits: { enabled: false },
			chart: {
				type: 'pie',
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				height: 290,
				backgroundColor: 'transparent',
			},
			title: {
				text: graph_heading,
				style: {
					'fontFamily': 'Ubuntu, sans-serif',
					'fontSize': '25px',
					'color' : ir_data.colors[0]
				}
			},
			tooltip: {
				pointFormat: '{point.y}: <b>{point.percentage:.1f}%</b>',
				valueSuffix: earnings.default_units_value
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false
					},
					showInLegend: true,
				}
			},
			colors: [ir_data.colors[0], ir_data.colors[1]],
			series: [
			{
				name: earnings.title,
				data: [
					{
						name: earnings.unpaid_label,
						y: parseFloat( un_paid_per ),
						sliced: false
				},
					{
						name: earnings.paid_label,
						y: parseFloat( paid_per ),
						sliced: false,
						selected: true
				}
				],
				innerSize: '60%',
			}
			]
		}
	);
}
