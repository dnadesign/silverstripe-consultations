<% include Header %>

<div class="banner_intro">
	<div class="wrapper">
		<h1>$Title</h1>

		<% include ShareThis %>
	</div>
</div>


<div class="search_content single_comment">
	<div class="wrapper">
		<div class="col_left col_left__wide">
			<ul class="search_results">
				<% with Submission %>
					<li class="search_result">
						<div class="search_result_inner">
							<% include Comment %>
						</div>
					</li>
				<% end_with %>
			</ul>
		</div>
	</div>
</div>