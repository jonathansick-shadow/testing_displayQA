#!/usr/bin/env bash

SCRIPTPATH=$(which $0)
DQAROOT=$(dirname $(dirname $SCRIPTPATH))

cat <<EOF

WARNING: Sourcing the setups.* scripts might break your lsst python
installation!  It will create __init__.py in python/lsst/ and may
conflict with the lsstimports module.  You should only do it if you're
working outside LSST.  In LSST, just use eups 'setup'.

EOF


# write the .sh
read -r -d '' SETUPSSH <<EOF
DQAROOT=$DQAROOT
PYPATH=\$DQAROOT/python

touch \$PYPATH/lsst/__init__.py

# if it's already setup, swap out the old python path, and put in the new one.
if [ -z "\$TESTING_DISPLAYQA_DIR" ]; then
    export PYTHONPATH=\$PYTHONPATH:\$PYPATH
    export TESTING_DISPLAYQA_DIR=\$DQAROOT
else
    export PYTHONPATH=\$(echo \$PYTHONPATH | sed s/\$TESTING_DISPLAYQA_DIR/\$DQAROOT/)
    export TESTING_DISPLAYQA_DIR=\$DQAROOT
fi
EOF

echo "$SETUPSSH" > $DQAROOT/setups.sh



# write the .csh
read -r -d '' SETUPSCSH <<EOF
set DQAROOT=$DQAROOT
set PYPATH=\$DQAROOT/python

touch \$PYPATH/lsst/__init__.py

# if it's already setup, swap out the old python path, and put in the new one.
if ("\$TESTING_DISPLAYQA_DIR" == "")  then
    setenv PYTHONPATH \$PYTHONPATH:\$PYPATH
    setenv TESTING_DISPLAYQA_DIR \$DQAROOT
else
    setenv PYTHONPATH \`echo \$PYTHONPATH | sed s/\$TESTING_DISPLAYQA_DIR/\$DQAROOT/g\`
    setenv TESTING_DISPLAYQA_DIR \$DQAROOT
endif
EOF

echo "$SETUPSCSH" > $DQAROOT/setups.csh


