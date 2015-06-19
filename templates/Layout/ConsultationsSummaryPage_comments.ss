<div class="banner_intro">
	<div class="wrapper">
		<h1>$Title</h1>

		<p class="intro">$Introduction</p>

		$ConsultationSelectorForm
	</div>
</div>

<% if Consultation && Consultation.Tagline %>
	<div class="wrapper">
		<div class="feature_text feature_tagline">
			$Consultation.Tagline
		</div>
	</div>
<% end_if %>


<div class="search_content">
	<div class="wrapper">
		<div class="col_left col_left__wide">
			<div class="search_sort">
				<ul class="search_sort_filters">
					<li class="first <% if CurrentSort == desc %>current<% end_if %>">
						<a href="$Link("comments?Sort=desc")" class="search_sort_filter">Newest First</a>
					</li>
				
					<li class="last <% if CurrentSort == asc %>current<% end_if %>">
						<a href="$Link("comments?Sort=asc")" class="search_sort_filter">Oldest First</a>
					</li>
				</ul>
			</div>

			<div class="search_count">
				<% if ResultsList %>
					<p class="search_count_text">Showing $ResultsList.getTotalItems submission<% if ResultsList.getTotalItems != 1 %>s<% end_if %><% if Idea %> for $Idea.Title<% end_if %><% if Suburbs %> from <% loop Suburbs %>$Title<% if not Last %>, <% end_if %><% end_loop %><% end_if %>.</p>
				<% else %>
					<p class="search_count_text">No submissions found <% if Idea %>for $Idea.Title <% end_if %><% if Suburbs %>from <% loop Suburbs %>$Title<% if not Last %>, <% end_if %><% end_loop %><% end_if %>.</p>
				<% end_if %>
			</div>

			<ul class="search_results">
				<% loop ResultsList %>
					<li class="search_result">
						<div class="search_result_inner">
							<% include Comment %>
						</div>
					</li>
				<% end_loop %>
			</ul>

			<% if ResultsList.NotLastPage %>
				<div class="search_result_show_more">
					<a href="$ResultsList.NextLink" class="button button__search_show_more search_show_more">
						Show More
					</a>
				</div>
			<% end_if %>
		</div>
	</div>
</div>