type routeHandler = () => Promise<void>;

export interface IController {
	[key: string]: routeHandler;
}
