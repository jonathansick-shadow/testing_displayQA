#!/usr/bin/env bash

SCRIPTPATH=$(which $0)
DQAROOT=$(dirname $(dirname $SCRIPTPATH))

cat <<EOF

Source the appropriate file to make displayQA python code visible
(ie. append to PYTHONPATH), and to set TESTING_DISPLAYQA_DIR so
python will know where to write tests and figures.

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
else
    export PYTHONPATH=\$(echo \$PYTHONPATH | sed s/\$TESTING_DISPLAYQA_DIR/\$DQAROOT/)
fi
export TESTING_DISPLAYQA_DIR=\$DQAROOT
EOF

echo "Writing $DQAROOT/setups.sh"
echo "$SETUPSSH" > $DQAROOT/setups.sh



# write the .csh
read -r -d '' SETUPSCSH <<EOF
set DQAROOT=$DQAROOT
set PYPATH=\$DQAROOT/python

touch \$PYPATH/lsst/__init__.py

# if it's already setup, swap out the old python path, and put in the new one.
if ("\$TESTING_DISPLAYQA_DIR" == "")  then
    setenv PYTHONPATH \$PYTHONPATH:\$PYPATH
else
    setenv PYTHONPATH \`echo \$PYTHONPATH | sed s/\$TESTING_DISPLAYQA_DIR/\$DQAROOT/g\`
endif
setenv TESTING_DISPLAYQA_DIR \$DQAROOT
EOF

echo "Writing $DQAROOT/setups.csh"
echo "$SETUPSCSH" > $DQAROOT/setups.csh


