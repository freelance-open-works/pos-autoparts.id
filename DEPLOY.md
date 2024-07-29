## How to deploy

temp

```bash
rsync -arP -e 'ssh -p 2233' \
--exclude=node_modules \
--exclude=.git \
--exclude=.env \
--exclude=database/database.sqlite \
--exclude=storage \
--exclude=public/hot \
. panelawan-template@ajikamaludin.id:/home/panelawan-template/htdocs/template.panelawan.my.id
```
