# This method copies all lines until a line starting with #DEV is encountered
# This marks the beginning of a dev-only section. Until another comment is 
# found, no lines will be copied!
TO=$1
FROM=$2
DO_COPY=1
touch $TO
while read l; do
    if [[ $l == \#DEV* ]]; then
        DO_COPY=0
    elif [[ $l == \#* ]]; then
        DO_COPY=1
    fi
    if [[ $DO_COPY == 1 ]]; then
        echo $l >> $TO
    fi
done <$FROM
