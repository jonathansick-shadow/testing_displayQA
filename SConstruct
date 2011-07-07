# -*- python -*-
#
# Setup our environment
#
import glob, os.path, re
import lsst.SConsUtils as scons

thisPkg    = "testing_displayQA"
pythonPkg  = "displayQaLib"
pythonPath = os.path.join("python", "lsst", "testing", "displayQA")


###############################################################################
# Boilerplate below here
try:
    scons.ConfigureDependentProducts
except AttributeError:
    import lsst.afw.SconsUtils
    scons.ConfigureDependentProducts = lsst.afw.SconsUtils.ConfigureDependentProducts

env = scons.makeEnv(thisPkg,
                    r"$HeadURL$",
                    scons.ConfigureDependentProducts(thisPkg))

env.thisPkg    = thisPkg
env.pythonPkg  = pythonPkg
env.pythonPath = pythonPath

env.Help("""
Pipeline output testing package
""")


# Build/install things
#SConscript(os.path.join(pythonPath, "SConscript"))

env['IgnoreFiles'] = r"(~$|\.pyc$|^\.svn$|\.o$)"
Alias("install", [
    env.Install(env['prefix'], "index.html"),
    env.Install(env['prefix'], "bin"),
    env.Install(env['prefix'], "doc"),
    env.Install(env['prefix'], "etc"),
    env.Install(env['prefix'], "examples"),
    env.Install(env['prefix'], "python"),
    env.Install(env['prefix'], "www"),
    env.InstallEups(os.path.join(env['prefix'], "ups")),
])
env.Declare()
scons.CleanTree(r"*~ core *.so *.os *.o")

