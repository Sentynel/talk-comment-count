WordPress plugin to show comment counts from Coral Talk, not the native DB

Server-side page generation time, rather than using the JS comment-count plugin which queries on
every page load. This does mean that the counts can get out of sync a bit, but it's not the end
of the world.

This is done via direct Mongo queries rather than asking the comment server; a better approach
would probably be to enable the talk-plugin-comment-count and then query the interface its
count.js queries.
