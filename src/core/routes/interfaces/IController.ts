type routeHandler = () => void;

export interface IController {
	[key: string]: routeHandler;
}
