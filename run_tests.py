import os, sys, importlib.util, traceback, mechanicalsoup
sys.path.insert(0, os.path.join(os.path.dirname(os.path.abspath(__file__)), "tests"))
from lib import backends
from lib.helpers import ROOT_DIR

FAILED = False
TESTS_RUN = 0
ENABLED_BACKENDS = [b.strip() for b in (os.environ.get("BAIKAL_TEST_BACKENDS") or "sqlite,mysql,pgsql").split(",")]
assert set(ENABLED_BACKENDS) <= {b.name for b in backends.ALL}, f"Unknown backend in BAIKAL_TEST_BACKENDS: {ENABLED_BACKENDS}"

def remove_config():
    yaml_path = os.path.join(ROOT_DIR, "config/baikal.yaml")
    if os.path.exists(yaml_path):
        try:
            with open(yaml_path, "r", encoding="utf-8") as f:
                content = f.read()
            if "abbc0ffed774e98a495fe5a2596ed7deab14211f250673ad661be7b759c9a7fd" not in content:
                # If this is contained, it is a test file that can safely be deleted.
                # Keep in sync with mod.ADMIN_PASSWORD
                assert False, "There already is a config file that was not created by a test. Stopping."
                
            os.remove(yaml_path)
        except Exception as e:
            print("Error while checking baikal.yaml:", e)
            sys.exit(1)

def load_tests(path):
    files = []
    for x in os.listdir(path):
        if x.endswith(".py"):
            files.append(os.path.join(path, x))
    assert files != []
    return sorted(files)

def run_file(path):
    global FAILED, TESTS_RUN
    test_folder = os.path.dirname(os.path.abspath(path))
    if test_folder not in sys.path:
        sys.path.insert(0, test_folder)
        
    spec = importlib.util.spec_from_file_location("mod", path)
    mod = importlib.util.module_from_spec(spec)
    
    try:
        spec.loader.exec_module(mod)
    except:
        print("ERR in import:", path)
        FAILED = True
        traceback.print_exc()
        return

    module_backends = getattr(mod, "BACKENDS", [backends.SQLITE])

    for n in dir(mod):
        if not n.startswith("test_"):
            continue
        for backend in module_backends:
            if backend.name not in ENABLED_BACKENDS:
                continue
            print(path, "::", n, "[", backend, "]", sep = '')
            remove_config()
            TESTS_RUN += 1

            browser = mechanicalsoup.StatefulBrowser()
            test_function = getattr(mod, n)
            try:
                backend.reset()
                test_function(browser, backend)
                print("[OK]")
            except:
                FAILED = True
                traceback.print_exc()
                print(browser.get_current_page())
                print("[FAIL]: ", path, "::", n, "[", backend, "]", sep = '')

def main():
    global FAILED
    
    if len(sys.argv) > 1:
        run_file(sys.argv[1])
    else:
        for f in load_tests("tests"):
            run_file(f)

    if TESTS_RUN == 0:
        print("No tests were run.")
        sys.exit(1)
    elif FAILED:
        print("Some tests failed.")
        sys.exit(1)
    else:
        print("All tests passed.")
        sys.exit(0)

if __name__ == "__main__":
    main()
