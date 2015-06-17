<% if Matches.MoreThanOnePage %>
	<p class="search_result_pagination">
		<% if Matches.NotFirstPage %>
			<a class="prev-page" href="$Matches.PrevLink" title="View the previous page">Previous</a>
		<% end_if %>
		<span>
			<% loop Matches.PaginationSummary(4) %>
				<% if CurrentBool %>
					<span class="current-page bold">$PageNum</span>
				<% else %>
					<% if Link %>
						<a href="$Link" title="View page number $PageNum">$PageNum</a>
					<% else %>
						&hellip;
					<% end_if %>
				<% end_if %>
			<% end_loop %>
		</span>
		<% if Matches.NotLastPage %>
			<a class="next-page" href="$Matches.NextLink" title="View the next page">Next</a>
		<% end_if %>
	</p>
<% end_if %>