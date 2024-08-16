TODO :

default count product must -> 507.317

rsync -arP -e 'ssh -p 2233' \
--exclude=node_modules \
--exclude=.git \
--exclude=public/hot \
--exclude=pos-app.zip \
--exclude=database/database.sqlite \
. panelawan-autoparts@172.16.1.6:/home/panelawan-autoparts/htdocs/autoparts.panelawan.my.id
