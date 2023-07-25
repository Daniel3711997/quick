export class Dispose {
    public static disposables: Array<() => void> = [];

    public static clear(): void {
        this.disposables = [];
    }

    public static add(disposable: () => void): void {
        this.disposables.push(disposable);
    }

    public static dispose(): void {
        const disposables = this.disposables;

        this.clear();
        disposables.forEach(disposable => disposable());
    }
}
