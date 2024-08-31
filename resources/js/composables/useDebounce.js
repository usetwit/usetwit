export function useDebounce(fn, delay = 300) {
    let timeout;
    let debouncedFn;

    debouncedFn = (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), delay);
    };

    return debouncedFn;
}
