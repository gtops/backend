import { nth } from "lodash";
import * as restify from "restify";
import { errors } from "../../api-errors";
import { ACL } from "../../middleware/ACL";
import { validateMiddleware } from "../../middleware/Validator";
import { IController } from "./interfaces/IController";
import { EMethod, IRouteDefinition } from "./interfaces/IRouteDefinition";
import { IRoutes } from "./interfaces/IRoutes";

export class Router {
	private readonly pathToControllers = "../../controllers";
	private controllers: IController = {};

	constructor(private readonly server: restify.Server, private readonly routes: IRoutes, private readonly acl: ACL) {}

	private static routeDefinition(key: string, handle: string): IRouteDefinition {
		return {
			method: nth(key.split(" "), 0).trim() as EMethod,
			path: nth(key.split(" "), 1).trim(),
			handler: nth(handle.split("."), 1).trim(),
			controller: nth(handle.split("."), 0).trim(),
		};
	}

	public async init(): Promise<void> {
		await this.loadControllers();
		for (const key of Object.keys(this.routes)) {
			const validator = this.routes[key].validate;
			const route = Router.routeDefinition(key, this.routes[key].handler);
			const Controller = this.controllers[route.controller];

			const method = Object.values(EMethod).includes(route.method) ? route.method : null;
			const { path } = route;
			const handle = Controller[route.handler];

			if (!method) {
				throw errors.NotAssignedRouteMethod;
			}
			if (!Controller) {
				throw errors.UnknownController;
			}
			if (!handle) {
				throw errors.UnknownRouteHandle;
			}

			if (validator) {
				this.server[method.toLocaleLowerCase()](path, validateMiddleware(validator), handle.bind(Controller));
			} else {
				this.server[method.toLocaleLowerCase()](path, handle.bind(Controller));
			}

			this.acl.allow(this.routes[key].allowRoles, path, method);
		}
	}

	private async loadControllers(): Promise<void> {
		const controllers = await import(this.pathToControllers);
		for (const key of Object.keys(controllers)) {
			const Controller = controllers[key];
			this.controllers[key] = new Controller();
		}
	}
}
